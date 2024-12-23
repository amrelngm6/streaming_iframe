<template>
    <div class="w-full flex overflow-auto" >
        
        <div  v-if="content " class=" w-full relative">
            
            <package_wizard :currency="currency" @callback="showWizard=false" v-if="showWizard" :fields="content.fillable" :key="showWizard" :item="activeItem" :setting="setting" :conf="conf" ></package_wizard>
            
            <div class=" " v-if="!showWizard && content.items && !content.items.length ">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center pt-15 pb-15">
                            <h2 class="fs-2x fw-bold mb-0" v-text="content.title"></h2>
                            <p class="text-gray-400 fs-4 font-semibold py-7" v-text="translate('Add your first package using this below wizard')"></p>
                            <a v-text="translate('add_new')" @click="addPackageWizard" href="javascript:;" class="text-white btn btn-primary er fs-6 px-8 py-4" ></a>
                        </div>

                        <div class="text-center pb-15 px-5">
                            <img :src="'/uploads/img/start-wizard.png'" alt="" class="mx-auto mw-100 h-200px h-sm-325px">          
                        </div>
                    </div>
                </div>
            </div>

            <main class=" flex-1 overflow-x-hidden overflow-y-auto  w-full relative" v-if="content.items && !showWizard && content.items.length">
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    
                    <h1  class="font-bold text-lg w-full" v-text="content.title"></h1>
                    <a href="javascript:;" v-if="!showWizard" class="uppercase p-2 mx-2 text-center text-white w-32 rounded-lg bg-danger" @click="addPackageWizard" v-text="translate('add_new')"></a>
                </div>
                <hr class="mt-2" />
                <div class="w-full bg-white" >

                    <datatabble  class="align-middle fs-6 gy-5 table table-row-dashed px-6" :body-text-direction="translate('is_rtl')" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                        <template #item-picture="item">
                            <img :src="item.picture" class="w-8 h-8 rounded-full" />
                        </template>

                        <template #item-single_trip="item">
                            
                        </template>

                        <template #item-double_trip="item">
                            
                            
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
                <!-- END New releases -->
            </main>
        </div>
    </div>
</template>
<script>

import delete_icon from '@/components/svgs/trash.vue';
import route_icon from '@/components/svgs/route.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails} from '@/utils.vue';


const form_field = defineAsyncComponent(() =>
  import('@/components/includes/form_field.vue')
);

import package_wizard from '@/components/wizards/packageWizard.vue';
import tooltip from '@/components/includes/tooltip.vue';




export default 
{
    components:{
        'datatabble': Vue3EasyDataTable,
        delete_icon,
        car_icon,
        route_icon,
        form_field,
        package_wizard, 
        tooltip
    },
    name:'Packages',
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';

        const showEditSide = ref(false);
        const activeItem = ref({feature:{}});
        const content = ref({});
        const showWizard =  ref(false);
        

        const closeSide = () => {
            showEditSide.value = false;
        }

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
            });
        }
        
        load();

        const addPackageWizard = () => 
        {
            activeItem.value = {feature:{}};
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

                case 'edit':
                    activeItem.value = data;
                    showWizard.value = true
                    break;  

                case 'delete':
                    deleteByKey('package_id', data, 'Package.delete');
                    break;  

                    
                case 'close':
                    showEditSide.value = false;
                    activeItem.value = {};
                    break;
            }
        }

        
        return {
            showEditSide,
            url,
            content,
            activeItem,
            showWizard,
            translate,
            closeSide,
            addPackageWizard,
            handleAction,
        };
    },

    props: [
        'path',
        'lang',
        'system_setting',
        'setting',
        'setting',
        'conf',
        'auth',
        'currency'
    ],
    
};
</script>