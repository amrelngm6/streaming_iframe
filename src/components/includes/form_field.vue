<template>
    <div class="w-full" v-if="column" >
        
        <input v-if="column && column.column_type == 'hidden'" :name="handleName(column)" type="hidden" v-model="item[column.key]">

        <input @change="changed(item[column.key])"  :required="column.required" :disabled="column.disabled" v-if="isInput(column.column_type)" autocomplete="off" :name="handleName(column)" :type="column.column_type" class="form-control form-control-solid" :placeholder="column.title" :max="column.max" :min="column.min" v-model="item[column.key]">
    
        <input :required="column.required" :disabled="column.disabled" v-if="column.column_type == 'password'" autocomplete="off" :name="handleName(column)" :type="column.column_type" class="form-control form-control-solid" :placeholder="column.title">

        <div v-if="column.column_type == 'checkbox'"  class="py-4 flex gap gap-2 cursor-pointer" @click="setActiveStatus(item, column.key)">
            <span :class="!item[column.key] ? 'bg-gray-200' : 'bg-red-400'" class="mx-2 mt-1 bg-red-400 block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: item[column.key] ? '16px' : 0}"></a></span>
            <span  v-text="item[column.key] ? translate('Active') : translate('Pending')" v-if="!column.hide_text" class=" font-semibold inline-flex items-center px-2 py-1 rounded-full text-xs font-medium "></span>
            <input :value="''" :checked="item[column.key] ? false : true"  type="checkbox" class="hidden" :name="handleName(column)" />
            <input  :value="'on'" :checked="item[column.key] ? true : false"  type="checkbox" class="hidden" :name="handleName(column)" />
        </div>
        
        <textarea  :required="column.required" :disabled="column.disabled" v-if="column.column_type == 'textarea'"  :name="handleName(column)" type="text" rows="4" class="mt-3 form-control form-control-solid" :placeholder="column.title" v-model="item[column.key]"></textarea>

        <Multiselect
            v-if="column.multiple && column.data && column.column_type == 'select'" 
            mode="tags"
            v-model="multipleValue"
            :object="false"
            :hideSelected="true"
            :searchable="true"
            :allowAbsent="true"
            :valueProp="column.column_key ? column.column_key : column.key"
            :trackBy="column.text_key"
            :label="column.text_key"    
            :options="column.data"
            :max="column.single ? 1 : (column.max ?? 100 )"
            @change="multiple_changed" 
        ></Multiselect>

        <input v-if="column.multiple && column.data && column.column_type == 'select'" type="hidden" v-for="selected in  item[column.column_key]" :name="'params['+(column.column_key)+'][]'" :value="selected[column.column_key]" />

        <select @change="changed(column.data ? column.data[column.data.findIndex(e => e[column.key] == item[column.key])] : e[column.key])" :required="column.required" :disabled="column.disabled" v-if="!column.multiple && column.data && column.column_type == 'select'" v-model="item[column.key]"  :name="handleName(column)" :type="column.column_type" class="form-control form-control-solid border-gray-400 border-1 border"   :placeholder="column.title">
            <option value="0"  v-if="!column.required" v-text="translate('select') +' '+ column.title"></option>
            <option v-for="option in column.data" :value="option[ column.column_key ? column.column_key : column.key]" v-text="option[column.text_key]"></option>
        </select>
        
        <vue-medialibrary-field @changed="multiple_changed" :key="item" v-if="column.column_type == 'file' || column.column_type == 'picture' "  :name="handleName(column)" :filepath="item[column.key]" :api_url="conf.url"></vue-medialibrary-field>

        <input type="hidden" v-model="item[column.key]"  :name="handleName(column)" v-if="column.column_type == 'editor'" />
        <ckeditor v-model="item[column.key]" v-if="column.column_type == 'editor'"
                :editor="editor" :config="editorConfig" />
    </div>
</template>
<script>
import close_icon from '@/components/svgs/Close.vue';
import field from '@/components/includes/Field.vue';
import { translate, handleGetRequest, handleName, isInput, setActiveStatus, handleRequest, deleteByKey, showAlert } from '@/utils.vue';

import Multiselect from '@vueform/multiselect'
import {ref} from 'vue'



import { ClassicEditor, FullPage , Bold, Essentials, Italic, Mention, Paragraph, Link, List, Table, TableToolbar, GeneralHtmlSupport,  SourceEditing, Image, ImageInsert, Undo, Heading, Font } from 'ckeditor5';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';

import 'ckeditor5/ckeditor5.css';


export default 
{
    components: {
        'vue-medialibrary-field': field,
        close_icon,
        Ckeditor,
        Multiselect 
    },

    props: [
        'column',
        'system_setting',
        'model',
        'model_id',
        'item',
        'conf',
    ],
    
    emits: ['callback'],

    setup(props, {emit}) {

        const changed = (newVak) => 
        {
            emit('callback', newVak, props.column.key);
        }

        const multiple_changed = (newVak) => 
        {
            // emit('callback', model.map(e => e[props.column.key]), props.column.key );
            emit('callback', newVak, props.column.key, true);

        }

        const multipleValue = ref([]);
        try {
            if (props.column.multiple)
                multipleValue.value = props.item[props.column.key] ? props.item[props.column.key].map(e => e[props.column.column_key]) : [];
            
        } catch (error) {
            try {
                multipleValue.value = props.item[props.column.key] ? JSON.parse(props.item[props.column.key]).map(e => e[props.column.column_key] ?? e) : [];
            } catch (error) {
                multipleValue.value = props.item[props.column.key] ? props.item[props.column.key].map(e => e[props.column.column_key] ?? e) : [];
            }
        }


        const editor = ClassicEditor;
        const editorConfig =  ref({
            allowedContent:true,
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            plugins: [ SourceEditing, Bold, Essentials, Italic, Mention, Paragraph,  Undo, Heading, Link, List, Image, Font,Table, TableToolbar, GeneralHtmlSupport  ],
            toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', 'heading', 'fontSize', 'fontColor' ,'link','insertImage', 'insertTable', 'sourceEditing', 'bulletedList', 'numberedList' ],
        });
        
        
        return {
            editor,
            editorConfig,
            file: '',
            multipleValue,
            isInput,
            setActiveStatus,
            changed,
            handleName,
            translate,
            multiple_changed,
            itemData: props.item,
        }
    }
};
</script>
