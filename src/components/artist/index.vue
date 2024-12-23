<template>
    <div class="w-full flex overflow-auto" >
        
        <div  v-if="content " class=" w-full relative">
            
            <div class=" " v-if="showWizard ">
                <artist_wizard :currency="currency" :langs="langs" @callback="()=> {activeItem = null; showWizard = false}" :conf="conf" :auth="auth" :item="activeItem" :path="path+'/'+(activeItem.customer_id ? activeItem.customer_id : 'new')" :system_setting="system_setting" :setting="setting"  />
            </div>

            <main class=" flex-1 overflow-x-hidden overflow-y-auto  w-full relative" v-if="content.items && !showWizard">
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    
                    <h1  class="font-bold text-lg w-full" v-text="content.title"></h1>
                </div>
                <div class="w-full bg-white" >
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5 w-full flex px-4">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <input type="text"  v-model="searchValue" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Report">
                            </div>
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

                            <div class="w-150px">
                                <select v-model="searchField" class="form-select form-select-solid select2-hidden-accessible" data-control="select2" data-hide-search="true" data-placeholder="Rating" data-kt-ecommerce-order-filter="rating" data-select2-id="select2-data-9-zple" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                                    <option v-for="col in content.columns" v-text="col.text" :value="col.value"></option>
                                </select>
                            </div>
                        </div>
    
                    </div>
                    <datatabble 
                        :search-field="searchField"
                        :search-value="searchValue" alternating class="align-middle fs-6 gy-5 table table-row-dashed px-6" :body-text-direction="translate('lang') == 'ar' ? 'right' : 'left'" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                        <template #item-picture="item">
                            <img :src="item.picture" class="w-8 h-8 rounded-full" />
                        </template>

                        <template #item-images="item">
                            
                            <div class="w-full h-8 relative flex">
                                <div v-for="(image, i) in item.images" :style="'left: '+(20 * i)+'px'" class="rounded-full w-8 h-8 left-0 top-0 absolute" >
                                    <img  v-if="i < 3" :key="i" class="rounded-full w-8 h-8 rounded-[50px] border-2 border-purple-800" :src="image.picture" /> 
                                </div>
                                <span class="flex absolute pt-2" :style="'left: '+((20 * (item.images.length < 3 ? item.images.length : 3) ) + 20)+'px'"> <span class="font-semibold  px-1" v-if="item.images" v-text="item.images.length"></span></span>
                            </div>
                        </template>

                        <template #item-info="item">
                            <button v-if="!item.not_editable" class="p-2  hover:text-gray-600 text-purple" @click="handleAction('info', item)">
                                <vue-feather class="w-5" type="info"></vue-feather>
                            </button>
                        </template>
                        <template #item-delete="item">
                            <button v-if="!item.not_removeable" class="p-2 hover:text-gray-600 text-red-500" @click="handleAction('delete', item)">
                                <delete_icon class="w-5"/>
                            </button>
                        </template>
                    </datatabble>

                </div>
                <!-- END New releases -->
            </main>
        </div>
    </div>
</template>
<script>

import delete_icon from '@/components/svgs/trash.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails} from '@/utils.vue';


const form_field = defineAsyncComponent(() =>
  import('@/components/includes/form_field.vue')
);

import tooltip from '@/components/includes/tooltip.vue';
const artist_wizard = defineAsyncComponent(() => import('@/components/artist/artist_wizard.vue') );


export default 
{
    components:{
        'datatabble': Vue3EasyDataTable,
        artist_wizard,
        delete_icon,
        car_icon,
        form_field,
        tooltip
    },
    name:'Customer',
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';

        const showEditSide = ref(false);
        const activeItem = ref({});
        const content = ref({});
        const showWizard =  ref(false);
        const fillable =  ref(['Info', 'Time','Start location','End location','Driver']);
        const searchValue = ref("");
        const searchField = ref("#");
        

        const closeSide = () => {
            showEditSide.value = false;
        }


        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
                searchField.value = content.value.columns[1].value;
            });
        }
        
        load();


        const addCustomerWizard = () => 
        {
            activeItem.value = {};
            showWizard.value = true;
        }
        

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

                case 'view':
                    break;

                case 'info':
                    activeItem.value = data;
                    showWizard.value = true
                    break;  

                case 'delete':
                    deleteByKey('customer_id', data, 'Customer.delete');
                    break;  

                    
                case 'close':
                    
                    showEditSide.value = false;
                    activeItem.value = {};
                    break;
            }
        }


        const showTip = ref({});
        
        return {
            showTip,
            showEditSide,
            url,
            content,
            fillable,
            activeItem,
            searchValue,
            searchField,
            translate,
            showWizard,
            closeSide,
            addCustomerWizard,
            handleAction,
        };
    },

    props: [
        'path',
        'langs',
        'system_setting',
        'business_setting',
        'setting',
        'conf',
        'currency',
        'auth',
    ],
    
};
</script>