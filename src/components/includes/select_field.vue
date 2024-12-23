<template>
    <div class="w-full" v-if="column" >
        
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
            :max="column.max ?? 100"
            @change="changed" 
        ></Multiselect>

        <select :id="'select-'+column.key" v-model="item[column.key]" @change="changed" :required="column.required" :disabled="column.disabled" v-if="!column.multiple && column.data && column.column_type == 'select'"   :name="handleName(column)" :type="column.column_type" class="form-control form-control-solid"   :placeholder="column.title">
            <option value="0"  v-if="!column.required" v-text="translate('select') +' '+ column.title"></option>
            <option v-for="option in column.data" :value="option[ column.column_key ? column.column_key : column.key]" v-text="option[column.text_key]"></option>
        </select>
        
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
        'model',
        'model_id',
        'item',
        'conf',
    ],
    
    emits: ['callback'],

    setup(props, {emit}) {

        const changed = (value) => 
        {
            emit('callback', value, props.column.key);
        }

        return {
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
