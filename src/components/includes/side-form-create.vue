<template>
    <div class="modal fade show" style="z-index:9999; background: rgba(0, 0, 0, 0.5);display: block;" >
        <div class="mx-auto mt-10 modal-dialog-centered mw-650px rounded-lg shadow-lg bg-white dark:bg-gray-700 relative  overflow-y-auto" >
            <form action="/api/create" method="POST" data-refresh="1" id="add-device-form" class="action   m-auto rounded-lg max-w-xl w-full pb-10">
                <div class="w-full flex">
                    <h1 class="w-full m-auto max-w-xl text-base mb-2 " v-text="translate('ADD_new')"></h1>
                    <span class="cursor-pointer py-1 px-2" @click="emitClose(model)"><close_icon /></span>
                </div>
                <input name="type" type="hidden" :value="model">
                <input name="params[active]" type="hidden" value="1">
                
                <div  v-for="column in columns" v-if="columns">
                    <div class="py-1 w-full pt-4" v-if="column" >

                        <label v-if="column.withLabel" class="text-lg pt-3 block" v-text="column.title"></label>
                        
                        <input v-if="column.column_type == 'hidden'" :name="'params['+column.key+']'" type="hidden" v-model="column.default">
                        
                        <input :required="column.required" v-if="isInput(column.column_type)" :name="'params['+column.key+']'"  autocomplete="off" :type="column.column_type" class="form-control form-control-solid" :placeholder="column.title">

                        <input v-if="column.column_type == 'password'" autocomplete="off" :name="'params['+column.key+']'" :type="column.column_type" class="form-control form-control-solid" :placeholder="column.title">

                        <textarea  :required="column.required"  v-if="column.column_type == 'textarea'" :name="'params['+column.key+']'" rows="4" class="mt-3 form-control form-control-solid" :placeholder="column.title"></textarea>

                        <Multiselect
                            v-if="column.multiple && column.data && column.column_type == 'select'" 
                            mode="tags"
                            v-model="multipleValue"
                            :object="false"
                            :hideSelected="true"
                            :placeholder="translate('Click here to choose')+' '+ column.title"
                            :searchable="true"
                            :allowAbsent="true"
                            :valueProp="column.column_key ? column.column_key : column.key"
                            :trackBy="column.text_key"
                            :label="column.text_key"    
                            :options="column.data"
                            :max="column.single ? 1 : (column.max ?? 100 )"
                        ></Multiselect>
                        <input v-if="column.multiple && column.data && column.column_type == 'select'" type="hidden" v-for="selected in  multipleValue" :name="'params['+(column.column_key)+'][]'" :value="selected[column.column_key] ?? selected" />
                        
                        <select  :required="column.required"  :name="'params['+column.key+']'" :type="column.column_type" class="form-control form-control-solid" v-if="column.data && column.column_type == 'select' && !column.multiple"  :placeholder="column.title">
                            <option value="0" v-if="!column.required" v-text="translate('-- Choose') +' '+ column.title"></option>
                            <option v-for="option in column.data" :value="option[column.column_key ? column.column_key : column.key]" v-text="option[column.text_key]"></option>
                        </select> 

                        <div v-if="column.column_type == 'checkbox' && !showLoader"  class="flex gap gap-2 cursor-pointer my-2" @click="setActiveStatus(column)">
                            <span :for="column.key" class="block" v-text="column.title"></span>
                            <span :class="!column.active ? 'bg-gray-200' : 'bg-red-400'" class="mx-2 mt-1 bg-red-400 block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: column.active ? '16px' : 0}"></a></span>
                            <span v-if="!column.without_text" v-text="column.active ? translate('Active') : translate('Pending')" class=" font-semibold inline-flex items-center px-2 py-1 rounded-full text-xs font-medium "></span>
                            <input v-model="column.active"  type="checkbox" class="hidden" :name="'params['+column.key+']'" />
                        </div>
                        
                        <vue-medialibrary-field :key="item" v-if="column.column_type == 'file'" :name="'params['+column.key+']'" :filepath="null" :api_url="conf.url"></vue-medialibrary-field>

                        <vue-medialibrary-field :key="item" v-if="column.column_type == 'picture' " :name="'params['+column.key+']'" :filepath="null"  :api_url="conf.url"></vue-medialibrary-field>
                    </div>

                </div>
                <div class="text-center">
                    <button class="w-150px mt-4 btn btn-primary hover:bg-red-800 px-6 text-white font-semibold" v-text="translate('save')"></button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import close_icon from '@/components/svgs/Close.vue';
import field from '@/components/includes/Field.vue';
import { translate, handleGetRequest, handleName, isInput, setActiveStatus, handleRequest, deleteByKey, showAlert } from '@/utils.vue';
import Multiselect from '@vueform/multiselect'

export default 
{
    components: {
        'vue-medialibrary-field': field,
        Multiselect, 
        close_icon
    },
    props: [
        'conf',
        'columns',
        'model',
    ],
    data() {
        return {
            file: '',
            multipleValue: [],
            showLoader: false,
        }
    },
    methods: {

        emitClose(model)
        {
            model ? this.$emit('callback', model) : '';
        },

        isInput(val)
        {
            switch (val) 
            {
                case 'text':
                case 'number':
                case 'email':
                case 'time':
                case 'date':
                case 'phone':
                case '':
                    return true;
                    break;
            }
            return false;
        },   
        setActiveStatus(column) {
            column.active = !column.active;
        },
        translate(i)
        {
            return translate(i);
        }
     
    }

};
</script>
