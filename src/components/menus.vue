<template>
    <div class=" w-full pb-20">

        <div class="relative w-full" v-if="showDetails ">
            <menu_wizard :key="activeItem" :pages="content.pages" :categories="content.categories" :active_links="content.items" :item="activeItem" @save="showBuilder" @close="showDetails = false" :conf="conf"></menu_wizard>
        </div>

        <main v-if="content && !showDetails " class="px-4 flex-1 overflow-x-hidden overflow-y-auto  w-full  mb-20">
            <!-- New releases -->
            <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
            </div>

            <div class="sm:grid sm:space-y-0 space-y-6 xl:!grid-cols-3 md:grid-cols-2 gap-6" >

                <div class="box mb-0 overflow-hidden p-4 bg-white rounded-xl" v-for="menu in menus">
                    <div class="box-body space-y-5">
                        <div class="flex">
                            <div class="sm:flex sm:space-x-3 sm:space-y-0 space-y-4 rtl:space-x-reverse">
                                <div class="space-y-1 my-auto py-2" v-if="menu">
                                    <h5 @click="showBuilder('view', menu)" class="cursor-pointer font-semibold text-lg leading-none" v-text="menu.name"></h5>
                                    <p class="text-gray-500 dark:text-white/70 font-semibold text-sm truncate" v-if="menu.children" v-text="translate('Edit this menu')"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer mt-6">
                        <div class="grid grid-cols-12 gap-x-3">
                            <div class="sm:col-span-2 col-span-4 "><span @click="showBuilder('view', menu)"
                                    class="px-3 rounded cursor-pointer inline-flex !p-1 flex-shrink-0 justify-center items-center gap-2 rounded-sm  font-medium bg-white text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-xs dark:bg-bgdark dark:text-white/70 dark:focus:ring-offset-white/10"><i
                                        class="fa fa-eye  py-1  px-2"></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>

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

const menu_wizard = defineAsyncComponent(() =>
  import('@/components/wizards/menuWizard.vue')
);

export default
{
    components: {
        SideFormCreate,
        SideFormUpdate,
        menu_wizard,
        delete_icon
    },  
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';
        
        const showAddSide = ref(false);
        const showEditSide = ref(false);
        const showDetails = ref(null);
        const activeItem = ref({});
        const activeLinks = ref({});
        const menus = ref([
            // {type: 'header', name:'Header menu'}, 
            {type: 'footer1', name:'Footer menu 1'}, 
            // {type: 'footer2', name:'Footer menu 2'}, 
        ]);

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
        const showBuilder =  (actionName, data) =>  {
            switch(actionName) 
            {

                case 'view':
                    showDetails.value = true;
                    activeItem.value = data
                    activeLinks.value = handleActiveLinks(data)
                    break;

                case 'save':
                    showDetails.value = true;
                    savePermissions(data);
                    break;

                case 'close':
                    showDetails.value = false;
                    break;
            }
        }

        const handleActiveLinks = (menu) => {
            var items = [];
            for (let i = 0; i < content.value.items.length; i++) {
                const element = content.value.items[i];
                if (element.type == menu.type)
                {
                    items[i] = element
                }
            }
            return items.filter(item => item != null);
        }

        return {
            menus,
            url,
            content,
            activeItem,
            showDetails,
            activeLinks,
            closeSide,
            translate,
            showBuilder
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