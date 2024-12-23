<template>
    <div class="w-full flex overflow-auto">

        

        <div v-if="content" class=" w-full relative">

            <div class=" " v-if="showWizard">
                <plugin_wizard :currency="currency" :langs="langs"
                    @callback="() => { activeItem = null; showWizard = false }" :conf="conf" :auth="auth"
                    :item="activeItem" :path="path + '/' + (activeItem.id ? activeItem.id : 'new')"
                    :system_setting="system_setting" :setting="setting" />
            </div>

            <main class=" flex-1 overflow-x-hidden overflow-y-auto  w-full relative"
                v-if="content.items && !showWizard">
                <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
                    <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
                </div>
                <div class="w-full ">

                    <!--begin::Connected Accounts-->
                    <div class="card mb-5 mb-xl-10">

                        <!--begin::Card body-->
                        <div class="card-body border-top pt-4 pb-0">
                        
                            <!--begin::Item-->
                            <div class="d-flex flex-stack mb-6">
                                <div class="d-flex">
                                    <img :src="'/metronic8/demo1/assets/media/svg/brand-logos/google-icon.svg'"
                                        class="w-30px me-6" alt="" />

                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-bold">{{translate('Title')}}</a>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <span>{{translate('Status')}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-for="plugin in content.items" class="card mb-5 mb-xl-10">
                        
                        <div class="card-body border-top p-9">

                            <div class="py-2">
                                <div class="d-flex flex-stack">
                                    <div class="d-flex">
                                        <img :src="'/metronic8/demo1/assets/media/svg/brand-logos/google-icon.svg'"
                                            class="w-30px me-6" alt="" />

                                        <div class="d-flex flex-column">
                                            <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-bold" v-text="plugin.name"></a>
                                            <div class="fs-6 fw-semibold text-gray-500" v-text="plugin.description"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="form-check form-check-solid form-check-custom form-switch">
                                            <input v-model="plugin.status" @change="setStatus(plugin)" class="form-check-input w-45px h-30px" type="checkbox"
                                                id="slackswitch" />
                                            <label class="form-check-label" for="slackswitch"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
<script>

import delete_icon from '@/components/svgs/trash.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, ref } from 'vue';
import { translate, handleGetRequest, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';


const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);

import tooltip from '@/components/includes/tooltip.vue';
const plugin_wizard = defineAsyncComponent(() => import('@/components/plugins/plugin_wizard.vue'));


export default
    {
        components: {
            'datatabble': Vue3EasyDataTable,
            plugin_wizard,
            delete_icon,
            car_icon,
            form_field,
            tooltip
        },
        name: 'Plugins',
        setup(props) {

            const url = props.conf.url + props.path + '?load=json';

            const showEditSide = ref(false);
            const activeItem = ref({});
            const content = ref({});
            const showWizard = ref(false);
            const fillable = ref(['Info', 'Time', 'Start location', 'End location', 'Driver']);
            const searchValue = ref("");
            const searchField = ref("#");


            const closeSide = () => {
                showEditSide.value = false;
            }


            const load = () => {
                handleGetRequest(url).then(response => {
                    content.value = JSON.parse(JSON.stringify(response))
                    searchField.value = content.value.columns[1].value;
                });
            }

            load();


            const addPluginWizard = () => {
                activeItem.value = {};
                showWizard.value = true;
            }


            const setStatus = (plugin) => {
                var params = new URLSearchParams();
                params.append('type', 'Plugin.update')
                params.append('params[id]', plugin.id)
                params.append('params[status]', plugin.status)
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
            const handleAction = (actionName, data) => {
                switch (actionName) {

                    case 'view':
                        break;

                    case 'edit':
                        activeItem.value = data;
                        showWizard.value = true
                        break;

                    case 'delete':
                        deleteByKey('id', data, 'Plugin.delete');
                        break;


                    case 'close':

                        showEditSide.value = false;
                        activeItem.value = {};
                        break;
                }
            }


            const showTip = ref({});

            return {
                setStatus,
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
                addPluginWizard,
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