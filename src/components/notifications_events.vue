<template>
    <div class="w-full overflow-auto" >
        
        
        <notification_event_wizard @callback="closeSide" :active_tab="defaultTab" :conf="conf" 
                v-if="showWizard" :system_setting="system_setting" :fillable="content.fillable_grouped"
                :key="showWizard"   :item="activeItem"  />

        <div class=" w-full">
            <main v-if="content && !showLoader && !showWizard" class=" flex-1 overflow-x-hidden overflow-y-auto  w-full">
                <!-- New releases -->
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
                </div>
                <div class="mx-2 bg-white px-4 rounded shadow-sm py-2 ">
                    
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5 w-full flex ">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <input type="text"  v-model="searchValue" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Report">
                            </div>
                            <div id="kt_ecommerce_report_views_export" class="d-none"><div class="dt-buttons btn-group flex-wrap">      <button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>Copy</span></button> <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>Excel</span></button> <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>CSV</span></button> <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="kt_ecommerce_report_views_table" type="button"><span>PDF</span></button> </div></div>
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <div class="w-150px">
                                <select v-model="searchField" class="form-select form-select-solid select2-hidden-accessible" data-control="select2" data-hide-search="true" data-placeholder="Rating" data-kt-ecommerce-order-filter="rating" data-select2-id="select2-data-9-zple" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                                    <option v-for="col in content.columns" v-text="col.text" :value="col.value"></option>
                                </select>
                            </div>

                        </div>

                        <a href="javascript:;" class="uppercase p-2 mx-2 text-center text-white w-32 rounded-lg bg-danger" @click="showWizard = true,activeItem = {}" v-text="translate('add_new')"></a>

                    </div>
                    <div class="relative w-full overflow-x-auto bg-white">

                        <datatabble 
                            :search-field="searchField"
                            :search-value="searchValue"
                            alternating class="align-middle fs-6 gy-5 table table-row-dashed px-6  overflow-x-auto"  :body-text-direction="translate('is_rtl')" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                            <template #item-edit="item">
                                <button v-if="!item.not_editable" class="p-2 hover:text-gray-600 text-purple" @click="handleAction('edit', item)">
                                    <vue-feather class="w-5" type="edit"></vue-feather>
                                </button>
                            </template>
                            <template #item-delete="item">
                                <button v-if="!item.not_removeable" class="p-2 hover:text-red-400 text-red-500 " @click="handleAction('delete', item)">
                                    <delete_icon class="w-5"/>
                                </button>
                            </template>
                        </datatabble>
                    </div>


                </div>
                <!-- END New releases -->
            </main>
        </div>
    </div>
</template>
<script>

import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, handleRequest, deleteByKey, showAlert} from '@/utils.vue';
import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';
import notification_event_wizard from '@/components/wizards/notificationEventWizard.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';


const SideFormCreate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-update.vue')
);
export default
{
    components: {
        'datatabble': Vue3EasyDataTable,
        SideFormCreate,
        SideFormUpdate,
        translate,
        close_icon,
        notification_event_wizard,
        delete_icon
    },  
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';
        
        const showAddSide = ref(false);
        const showWizard = ref(false);
        const defaultTab = ref(false);
        const showEditSide = ref(false);
        const showDetails = ref(null);
        const activeItem = ref({});

        const content =  ref({
            title: '',
            items: [],
            columns: [],
        });

        const searchField = ref("payment_id");
        const searchValue = ref("");

        function load()
        {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
                searchField.value = content.value.columns[1].value;
            });
        }
        
        load();

        const closeSide = () => {
            showAddSide.value = false;
            showEditSide.value = false;
            showWizard.value = false;
        }

        
        const setActiveStatus = (item) => {
            item.status = !item.status;
        }

        

        /**
         * Handle actions from datatable buttons
         * Called From 'dataTableActions' component
         * 
         * @param String actionName 
         * @param Object data
         */  
        const handleAction =  (actionName, data) =>  {
            
            activeItem.value = data
            switch(actionName) 
            {

                case 'view':
                    showEditSide.value = false;
                    showAddSide.value = false;
                    showDetails.value = true;
                    break;

                case 'edit':
                    showWizard.value = true;
                    break;

                case 'delete':
                    deleteByKey('id', data, 'NotificationEvent.delete');
                    break;

            }
        }

        return {
            showWizard,
            defaultTab,
            showAddSide,
            showEditSide,
            url,
            content,
            activeItem,
            showDetails,
            setActiveStatus,
            closeSide,
            translate,
            searchField,
            searchValue,
            handleAction
        };
    },

    props: [
        'path',
        'lang',
        'system_setting',
        'conf',
        'auth',
    ],
};


</script>
<style lang="css">
    .rtl #side-cart-container
    {
        right: auto;
        left:0;
    }
</style>