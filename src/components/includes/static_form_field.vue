<template>
    <div class="w-full" v-if="column" >
        
        <input @change="callback" v-if="column && column.column_type == 'hidden'" :name="handleName(column)" type="hidden" v-model="itemData[column.key]">
        
        <div class="mb-10 fv-row">
            <label class="required form-label" v-text="column.title"></label>
            <input  @change="callback" v-if="isInput(column.column_type)" :type="column.column_type" :name="handleName(column)" class="form-control mb-2" :placeholder="column.placeholder" v-model="itemData[column.key]" :required="column.required" :disabled="column.disabled"  />
            <div class="text-muted fs-7"  v-text="column.description"></div>
        </div>

        <div v-if="column.column_type == 'checkbox'"  class="py-4 flex gap gap-2 cursor-pointer" @click="setActiveStatus(itemData, column.key)">
            <span :class="!itemData[column.key] ? 'bg-gray-200' : 'bg-red-400'" class="mx-2 mt-1 bg-red-400 block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: itemData[column.key] ? '16px' : 0}"></a></span>
            <span  v-text="itemData[column.key] ? translate('Active') : translate('Pending')" v-if="!column.hide_text" class=" font-semibold inline-flex items-center px-2 py-1 rounded-full text-xs font-medium "></span>
            <input v-if="itemData[column.key]" v-model="itemData[column.key]"  type="hidden" class="hidden"  :name="handleName(column)" />
            <input v-model="itemData[column.key]"  type="checkbox" class="hidden" :name="handleName(column)" />
        </div>
        
        <textarea  @change="callback" :required="column.required" :disabled="column.disabled" v-if="column.column_type == 'textarea'"  :name="handleName(column)" type="text" rows="4" class="mt-3 form-control form-control-solid" :placeholder="column.title" v-model="itemData[column.key]"></textarea>

    </div>
</template>
<script>
import close_icon from '@/components/svgs/Close.vue';
import field from '@/components/includes/Field.vue';
import { translate, handleGetRequest, handleName, isInput, setActiveStatus, handleRequest, deleteByKey, showAlert } from '@/utils.vue';

import Multiselect from '@vueform/multiselect'
import {ref} from 'vue'

export default 
{
    components: {
        'vue-medialibrary-field': field,
        close_icon,
        Multiselect 
    },

    props: [
        'column',
        'system_setting',
        'item',
        'conf',
    ],
    
    emits: ['callback'],

    setup(props, {emit}) {

        const callback = (model) => 
        {
            model ? emit('callback', model) : '';
        }

        const value = ref([]);

        const options  =  [
        ]

        return {
            file: '',
            callback,
            isInput,
            value,
            options,
            setActiveStatus,
            handleName,
            itemData: props.item,
            translate
        }
    }
};
</script>
