<template>
    <div class="w-full flex overflow-auto" >
        <div  v-if="content && !showWizard" class=" w-full relative">
            
            <div class=" " v-if="content.items && !content.items.length ">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center pt-15 pb-15">
                            <h2 class="fs-2x fw-bold mb-0" v-text="content.title"></h2>
                            <p class="text-gray-400 fs-4 font-semibold py-7" v-text="translate('Here you will se the list of the sent messages')"></p>
                        </div>

                        <div class="text-center pb-15 px-5">
                            <img :src="'/uploads/img/start-wizard.png'" alt="" class="mx-auto mw-100 h-200px h-sm-325px">          
                        </div>
                    </div>
                </div>
            </div>

            <main class=" flex-1 overflow-x-hidden overflow-y-auto  w-full relative" v-if="content.items">
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    <h1  class="font-bold text-lg w-full" v-text="content.title"></h1>
                </div>
                <hr class="mt-2" />
                <div class="w-full bg-white" >
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
                                    <option v-for="col in content.columns" v-text="col.text" :value="col.value"></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <datatabble  
                        :search-field="searchField"
                        :search-value="searchValue"
                        alternating
                        class="align-middle fs-6 gy-5 table table-row-dashed px-6" :body-text-direction="translate('is_rtl')" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                        <template #item-picture="item">
                            <img :src="item.picture" class="w-8 h-8 rounded-full" />
                        </template>

                        <template #item-edit="item">
                            <button v-if="!item.not_editable" class="p-2  hover:text-gray-600 text-purple" @click="handleAction('edit', item)">
                                <vue-feather class="w-5" type="edit"></vue-feather>
                            </button>
                        </template>
                        <template #item-delete="item">
                            <button v-if="!item.not_removeable" class="p-2 hover:text-gray-600 text-red-500" @click="handleAction('delete', item)">
                                <delete_icon class="w-5"/>
                            </button>
                        </template>
                    </datatabble>
                </div>
            </main>
    
        </div>
    </div>
</template>
<script>

import delete_icon from '@/components/svgs/trash.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, deleteByKey} from '@/utils.vue';


export default 
{
    components:{
        'datatabble': Vue3EasyDataTable,
        delete_icon,
    },
    name:'PaymentMethods',
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';

        const showWizard = ref(false);
        const showAddSide = ref(false);
        const activeItem = ref({});
        const content = ref({languages:[]});
        
        const closeSide = () => {
            showWizard.value = false;
            showAddSide.value = false;
        }


        const searchField = ref("email");
        const searchValue = ref("");

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
            });
        }
        
        load();

        /**
         * Handle actions from datatable buttons
         * Called From 'dataTableActions' component
         * 
         * @param String actionName 
         * @param Object data
         */  
         const handleAction =  (actionName, data) =>  {
            switch(actionName) 
            {

                case 'edit':
                    showAddSide.value = false
                    activeItem.value = data;
                    showWizard.value = true
                    break;  

                case 'delete':
                    deleteByKey('message_id', data, 'ContactForm.delete');
                    break;  

                    
                case 'close':
                    showAddSide.value = false;
                    showWizard.value = false;
                    activeItem.value = {};
                    break;
            }
        }

        
        return {
            searchField,
            searchValue,
            showAddSide,
            showWizard,
            url,
            content,
            activeItem,
            translate,
            closeSide,
            handleAction,
        };
    },

    props: [
        'path',
        'langs',
        'system_setting',
        
        'setting',
        'conf',
        'auth',
    ],
    
};
</script>