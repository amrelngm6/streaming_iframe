<template>
    <div class="w-full " >
        <div class=" " v-if="content.items && !content.items.length">
            <div class="card">
                <div class="card-body">
                    <div class="card-px text-center pt-15 pb-15">
                        <h2 class="fs-2x fw-bold mb-0" v-text="content.title"></h2>
                        <p class="text-gray-400 fs-4 font-semibold py-7"
                            v-text="translate('Empty data')"></p>
                        <a  v-if="!content.no_create" v-text="translate('add_new')" @click="openCreate()"
                            href="javascript:;" class="text-white btn btn-primary er fs-6 px-8 py-4"></a>
                    </div>

                    <div class="text-center pb-15 px-5">
                        <img :src="'/uploads/img/start-wizard.png'" alt="" class="mx-auto mw-100 h-200px h-sm-325px">
                    </div>
                </div>
            </div>
        </div>
        <div class=" w-full flex overflow-auto">

            <main v-if="content && content.items.length" class=" flex-1 overflow-x-hidden overflow-y-auto  w-full">
                <!-- New releases -->
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
                </div>
                <div class="mx-2 bg-white px-4 rounded shadow-sm py-2  ">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5 w-full flex ">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <input type="text"  v-model="searchValue" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Report">
                            </div>
                        </div>
                        <!--end::Card title-->

                        <!--begin::Card toolbar-->
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

                            <div class="w-150px">
                                <select v-model="searchField" class="form-select form-select-solid select2-hidden-accessible" data-control="select2" data-hide-search="true" data-placeholder="Rating" data-kt-ecommerce-order-filter="rating" data-select2-id="select2-data-9-zple" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                                    <option v-for="col in content.columns" v-text="col.text" :value="col.value"></option>
                                </select>
                            </div>
                        </div>
    
                        <a href="javascript:;" class="uppercase p-2 mx-2 text-center text-white w-32 rounded-lg bg-danger" @click="openCreate()" v-if="!content.no_create" v-text="translate('add_new')"></a>

                    </div>
                    <datatabble 
                        :search-field="searchField"
                        :search-value="searchValue" alternating class="align-middle fs-6 gy-5 table table-row-dashed px-6" :body-text-direction="translate('is_rtl')" fixed-checkbox v-if="content.columns" :headers="content.columns" :items="content.items" >

                        <template #item-picture="item">
                            <img :src="item.picture" class="w-8 h-8 rounded-full" />
                        </template>

                        <template #item-logo="item">
                            <img :src="item.logo" class="w-8 h-8 rounded-full" />
                        </template>


                        <template #item-item="item">
                            <div class="flex gap-2" v-if="item.item" >
                                <img :src="item.item.picture" class="w-8 h-8 rounded-full" />
                                <span class="py-2" v-text="item.item.lang_content.title ?? ''"></span>
                            </div>
                        </template>

                        <template #item-user="item">
                            <div class="flex gap-2" v-if="item.user" >
                                <img :src="item.user.picture" class="w-8 h-8 rounded-full" /> <span class="py-2" v-text="item.user.name"></span>
                            </div>
                        </template>
                        <template #item-customer="item">
                            <div class="flex gap-2" v-if="item.customer" >
                                <img :src="item.customer.picture" class="w-8 h-8 rounded-full" /> <span class="py-2" v-text="item.customer.name"></span>
                            </div>
                        </template>
                        
                        <template #item-customers="item">
                            
                            <div class="w-full h-8 relative flex">
                                <div  v-for="(customer, i) in item.customers" :style="'left: '+(20 * i)+'px'" class="rounded-full w-8 h-8 left-0 top-0 absolute" >
                                    <img v-if="i < 3" :key="i" class="rounded-full w-8 h-8 rounded-[50px] border-2 border-purple-800" :src="(customer && customer.picture) ? customer.picture : 'https://via.placeholder.com/37x37'" /> 
                                </div>
                                <span class="flex absolute pt-2" :style="'left: '+((20 * (item.customers.length < 3 ? item.customers.length : 3) ) + 20)+'px'"> <route_icon /><span class="font-semibold  px-1" v-if="item.customers" v-text="item.customers.length"></span></span>
                            </div>
                            
                        </template>

                        <template #item-edit="item">
                            <button v-if="!item.not_editable" class="p-2  hover:text-gray-600 text-purple" @click="handleAction('edit', item)">
                                <vue-feather class="w-5" type="edit"></vue-feather>
                            </button>
                        </template>
                        <template #item-delete="item">
                            <button v-if="!item.not_removeable" class="p-2 hover:text-gray-600 text-red-500" @click="handleAction('delete', item)">
                                <vue-feather class="w-5" type="x-circle"></vue-feather>
                            </button>
                        </template>
                    </datatabble>
                </div>
                
            </main>
            
            <side_form_create ref="activeFormCreate" @callback="closeSide" :auth="auth" :conf="conf" :model="(content.object_name ?? object_name)+'.create'" v-if="showAddSide && !showEditSide" :columns="content.fillable"  />
        
            <side-form-update ref="activeFormUpdate" @callback="closeSide" :key="activeItem" :auth="auth" :conf="conf" :model="(content.object_name ?? object_name)+'.update'" v-if="showEditSide && !showAddSide" :item="activeItem" :model_id="activeItem[object_key]" :index="object_key"  :columns="content.fillable"  />
        </div> 
    </div>
</template>
<script>

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import {defineAsyncComponent, ref} from 'vue';
const SideFormCreate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-create.vue')
);
const SideFormUpdate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-update.vue')
);

import close_icon from '@/components/svgs/trash.vue';
import {translate, handleGetRequest, deleteByKey} from '@/utils.vue';



export default 
{
    components:{
        'datatabble': Vue3EasyDataTable,
        'side_form_create': SideFormCreate,
        SideFormUpdate,
        translate,
        close_icon
    },
    setup(props) {
        
        const showAddSide = ref(false);
        const showEditSide = ref(false);

        const url =  props.conf.url+props.path+'?load=json';

        const content =  ref({
                title: '',
                items: [{}],
                columns: [],
            });
        
        const activeItem = ref({});

        const showWizard = ref(null);

        function openCreate() 
        {
            showAddSide.value = true; 
            showEditSide.value = false; 
        }

        const searchField = ref("#");
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
            showAddSide.value = false;
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
                    showAddSide.value = false; 
                    showEditSide.value = true; 
                    break;  


                case 'delete':
                    var name = content.value.object_name ?? props.object_name ;
                    var key = content.value.object_key ?? props.object_key ;
                    deleteByKey(key, data, name + '.delete');
                    break;  
            }
        }

        
        return {
            showAddSide,
            showEditSide,
            closeSide,
            openCreate,
            content,
            activeItem,
            showWizard,
            searchField,
            searchValue,
            translate,
            handleAction,
            url,
        }
        
    },
    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
        'object_name',
        'object_key',
        'currency'
    ],
    
};
</script>