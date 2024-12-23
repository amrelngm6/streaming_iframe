<template>
    <div class=" w-full pb-20">

        <div class="relative w-full" v-if="showDetails ">
            <permissions :key="activeItem" :item="activeItem" @save="handleAction" @close="handleAction" :conf="conf"></permissions>
        </div>

        <main v-if="content && !showDetails " class="px-4 flex-1 overflow-x-hidden overflow-y-auto  w-full  mb-20">
            <!-- New releases -->
            <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
                <!-- <a href="javascript:;" class="uppercase p-2 mx-2 text-center text-white w-32 rounded-lg bg-danger" @click="showAddSide = true,activeItem = {} " v-text="translate('add_new')"></a> -->
            </div>

            <div class="sm:grid sm:space-y-0 space-y-6 xl:!grid-cols-3 md:grid-cols-2 gap-6" >

                <div class="box mb-0 overflow-hidden p-4 bg-white rounded-xl" v-for="role in content.items">
                    <div class="box-body space-y-5">
                        <div class="flex">
                            <div class="sm:flex sm:space-x-3 sm:space-y-0 space-y-4 rtl:space-x-reverse">
                                <div class="space-y-1 my-auto py-2">
                                    <h5 @click="handleAction('view', role)" class="cursor-pointer font-semibold text-lg leading-none" v-text="role.name"></h5>
                                    <p class="text-gray-500 dark:text-white/70 font-semibold text-sm truncate"  v-text="translate('Users count') +' : '+ role.users_count"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="box-footer mt-6">
                        <div class="grid grid-cols-12 gap-x-3">
                            <div class="sm:col-span-2 col-span-4 "><span @click="handleAction('view', role)"
                                    class="px-3 rounded cursor-pointer inline-flex !p-1 flex-shrink-0 justify-center items-center gap-2 rounded-sm  font-medium bg-white text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-xs dark:bg-bgdark dark:text-white/70 dark:focus:ring-offset-white/10"><i
                                        class="fa fa-eye  py-1  px-2"></i></span></div>
                            <div @click="handleAction('edit', role)" class="sm:col-span-8 col-span-4"><span
                                    class="px-3 rounded cursor-pointer inline-flex !p-1 flex-shrink-0 justify-center items-center gap-2 w-full rounded-sm  font-medium bg-white text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-xs dark:bg-bgdark dark:text-white/70 dark:focus:ring-offset-white/10"><i
                                        class="fa fa-edit py-1"></i> <span class="text-base  "
                                        v-text="translate('Edit')"></span></span></div>
                            <div class="sm:col-span-2 col-span-4">
                                <div class="hs-dropdown ti-dropdown flex justify-end">
                                    <div @click="handleAction('delete', role)"
                                        class="w-full px-3 rounded cursor-pointer hs-dropdown-toggle ti-dropdown-toggle inline-flex !p-1 flex-shrink-0 justify-center items-center gap-2 rounded-sm  font-medium bg-white text-red-500 hover:text-red-400  align-middle focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-xs dark:bg-bgdark dark:text-white/70 dark:focus:ring-offset-white/10">
                                        <delete_icon class="w-4" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>

        <side-form-update :conf="conf" @callback="closeSide" model="Role.update" :item="activeItem" :model_id="activeItem.id"
            :index="activeItem.id" v-if="showEditSide && !showAddSide" :columns="content.fillable"
            class="col-md-3" />

        <side-form-create :conf="conf" @callback="closeSide" model="Role.create" v-if="showAddSide && content && content.fillable" :columns="content.fillable"  />
    </div>
</template>
<script>

import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, handleRequest, deleteByKey, showAlert} from '@/utils.vue';

import delete_icon from '@/components/svgs/trash.vue';

const SideFormCreate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-update.vue')
);

const permissions = defineAsyncComponent(() =>
  import('@/components/permissions.vue')
);

export default
{
    components: {
        SideFormCreate,
        SideFormUpdate,
        permissions,
        delete_icon
    },  
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';
        
        const showAddSide = ref(false);
        const showEditSide = ref(false);
        const showDetails = ref(null);
        const activeItem = ref({});

        const content =  ref({
            title: '',
            items: [],
            columns: [],
        });

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
            });
        }
        
        load();

        const closeSide = () => {
            showAddSide.value = false;
            showEditSide.value = false;
        }

        
        
        const savePermissions = (data) =>
        {
            var params = new URLSearchParams();
            params.append('type', 'Role.updatePermissions')
            params.append('params', JSON.stringify(data))
            handleRequest(params, '/api/update').then(response => {
                showAlert(response.result);
            })
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
                    showEditSide.value = false;
                    showAddSide.value = false;
                    showDetails.value = true;
                    activeItem.value = data
                    break;

                case 'save':
                    showEditSide.value = false;
                    showAddSide.value = false;
                    showDetails.value = true;
                    savePermissions(data);
                    break;

                case 'edit':
                    showEditSide.value = true;
                    showDetails.value = false;
                    showAddSide.value = false;
                    activeItem.value = data
                    break;

                case 'delete':
                    deleteByKey('id', data, 'Role.delete');
                    break;

                case 'close':
                    
                    showEditSide.value = false;
                    showAddSide.value = false;
                    showDetails.value = false;
                    break;
            }
        }

        return {
            showAddSide,
            showEditSide,
            url,
            content,
            activeItem,
            showDetails,
            closeSide,
            translate,
            handleAction
        };
    },

    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
    ],
};

</script>