<template>
    <div class="w-full " >
        
        <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
            <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
        </div>
        <div class="mx-2 bg-white px-4 rounded shadow-sm py-2 ">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5 w-full flex ">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <input type="text"  v-model="searchValue" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-125px " placeholder="Search Report">
                    </div>

                    <div id="kt_ecommerce_report_views_export" class="d-none"><div class="dt-buttons btn-group flex-wrap">      <button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>Copy</span></button> <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>Excel</span></button> <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>CSV</span></button> <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>PDF</span></button> </div></div>
                </div>

                <div class="card-toolbar flex-row-fluid justify-content-end gap-5 w-200px">

                    <div class="w-150px">
                        <select v-model="searchField" class="form-select form-select-solid select2-hidden-accessible" data-control="select2" data-hide-search="true" data-placeholder="Rating" data-kt-ecommerce-order-filter="rating" data-select2-id="select2-data-9-zple" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                            <option v-for="col in content.columns" v-text="col.text"  :value="col.value" ></option>
                        </select>
                    </div>

                </div>

                <div class="card-toolbar w-full flex-end">

                    <div class="w-full">
                        <vue-tailwind-datepicker 
                            :formatter="formatter"
                            @update:model-value="handleSelectedDate($event)"
                            :separator="' - '+translate('To')+' - '"
                            v-model="dateValue" />
                    </div>

                </div>
            </div>
            <datatabble 
                :search-field="searchField"
                :search-value="searchValue"
                alternating
                class="align-middle fs-6 gy-5 table table-row-dashed px-6" :body-text-direction="translate('is_rtl')" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                <template #item-model="item">
                    <div class="flex gap-2" v-if="item.model" >
                        <img :src="item.model.picture" class="w-8 h-8 rounded-full" />
                        <span class="py-2" v-text="item.model.name"></span>
                    </div>
                </template>

                <template #item-amount="item">
                    <span class="py-2" v-text="item.amount"></span>
                </template>

                <template #item-delete="item">
                    <button v-if="!item.not_removeable" class="p-2 hover:text-gray-600 text-red-500" @click="handleAction('delete', item)">
                        <vue-feather class="w-5" type="x-circle"></vue-feather>
                    </button>
                </template>
            </datatabble>
        </div>
    </div>
</template>
<script>

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import {ref} from 'vue';
import {translate, handleGetRequest, deleteByKey} from '@/utils.vue';
import VueTailwindDatepicker from "vue-tailwind-datepicker";
    
export default
{
    components: {
        'datatabble': Vue3EasyDataTable,
        VueTailwindDatepicker
    },
    
    setup(props) {
        
        const dateValue = ref({
            startDate: "",
            endDate: "",
        });

        const formatter = ref({
            date: "YYYY-MM-DD",
            month: "MMM",
        });
        
        const showEditSide = ref(false);

        const url =  props.conf.url+props.path+'?load=json';

        const content =  ref({
                title: '',
                items: [],
                columns: [],
            });
        
        const activeItem = ref({});

        const showLoader = ref(null);

        const searchField = ref("transaction_id");
        const searchValue = ref("");

        function load()
        {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
                searchField.value = content.value.columns[1].value;
            });
        }
        
        load();

        function closeSide (data) 
        {
            showEditSide.value = false;
        }

        /**
         * Handle actions from datatable buttons
         * Called From 'dataTableActions' component
         * 
         * @param String actionName 
         * @param Object data
         */  
        function  handleAction(actionName, data) {
            switch(actionName) 
            {
                case 'edit':
                    activeItem.value = data;
                    showEditSide.value = true; 
                    break;  

                case 'delete':
                    deleteByKey(props.object_key, data, props.object_name + '.delete');
                    break;  
            }
        }

        const handleSelectedDate = (event) => {
            console.log(event);
            handleGetRequest( props.conf.url+props.path+'?start_date='+event.startDate+'&end_date='+event.endDate+'&load=json' ).then(response=> {
            console.log(response);
                content.value = JSON.parse(JSON.stringify(response))
            });
        }
        
        return {
            handleSelectedDate,
            dateValue,
            formatter,
            showEditSide,
            closeSide,
            url ,
            content,
            activeItem,
            showLoader,
            translate,
            searchField,
            searchValue,
            handleAction
        };
    },
    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
        'currency',
    ],
};
</script>
