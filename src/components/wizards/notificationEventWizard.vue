<template>
    <div class="w-full ">
        
        <div v-if="loader" :key="loader" class="bg-white fixed w-full h-full top-0 left-0" style="z-index:99999; opacity: .9;">
            <img class="m-auto w-500px" :src="'/uploads/loader.gif'" />
        </div>
        <div class="w-full flex overflow-auto">
            <div class=" w-full relative">
                <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
                <div class=" card w-full py-10">
                    <div class="w-full stepper stepper-links ">
                        <div class="stepper-nav justify-content-center py-2 mb-10">
                            <div class="stepper-item " v-for="row in tabs" @click="activeTab = row">
                                <h3 :class="activeTab == row ? 'text-danger border-danger' : 'text-gray-400 border-transparent'"
                                    class="cursor-pointer pb-3 px-2 stepper-title text-md border-b " v-text="translate(row)"></h3>
                            </div>
                        </div>
                        <div class="w-full">
                            
                            

                            <div class="" v-if="activeTab == 'Info'" :key="activeTab">
                                <div class="card-body pt-0"  >
                                    <div class="settings-form" >
                                        <div class="max-w-xl mb-6 mx-auto row" >
                                            
                                            <label v-text="fillable.title.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.title" />
                                            <span v-text="fillable.title.help_text" v-if="fillable.title.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />
                                            
                                            <label v-text="fillable.receiver_model.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.receiver_model" />
                                            <span v-text="fillable.receiver_model.help_text" v-if="fillable.receiver_model.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />

                                            <label v-text="fillable.model.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.model" />
                                            <span v-text="fillable.model.help_text" v-if="fillable.model.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />

                                        </div>
                                    </div>
                                </div>
                                <p class="text-center mt-10"><a href="javascript:;" class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger" @click="activeTab = 'Content'" v-text="translate('Next')"></a></p>
                            </div>

                            
                            <div class="w-full  mx-auto" v-if="activeTab == 'Content'" :key="activeTab">
                                <div class="card-body pt-0"  >
                                    <div class="settings-form" >
                                        <div class="max-w-xl mb-6 mx-auto row" >
                                           
                                            <label v-text="fillable.subject.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.subject" />
                                            <span v-text="fillable.subject.help_text" v-if="fillable.subject.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />
                                           
                                            <label v-text="fillable.template_id.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.template_id" />
                                            <span v-text="fillable.template_id.help_text" v-if="fillable.template_id.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />

                                            <label v-text="fillable.body_text.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.body_text" />
                                            <span v-text="fillable.body_text.help_text" v-if="fillable.body_text.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />

                                        </div>
                                    </div>
                                </div>
                                <p class="text-center mt-10"><a href="javascript:;" class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger" @click="activeTab = 'Fields'" v-text="translate('Next')"></a></p>
                            </div>

                            <div class="" v-if="activeTab == 'Fields'" :key="activeTab">
                                <div class="card-body pt-0"  >
                                    <div class="settings-form" >
                                        <div class="max-w-xl mb-6 mx-auto row" >

                                            <label v-text="fillable.action.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                            <form_field class="flex-end" :item="activeItem" :column="fillable.action" />
                                            <span v-text="fillable.action.help_text" v-if="fillable.action.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                            <hr class="block mt-6 my-2 opacity-10" />

                                            <div v-if="activeItem.action == 'update'">
                                                <label v-text="fillable.action_field.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                                <form_field class="flex-end" :item="activeItem" :column="fillable.action_field" />
                                                <span v-text="fillable.action_field.help_text" v-if="fillable.action_field.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                                <hr class="block mt-6 my-2 opacity-10" />
                                            </div>

                                            <div v-if="activeItem.action_field != ''">
                                                <label v-text="fillable.action_value.title" class="col-form-label required fw-semibold fs-4 fw-bold" ></label>
                                                <form_field class="flex-end" :item="activeItem" :column="fillable.action_value" />
                                                <span v-text="fillable.action_value.help_text" v-if="fillable.action_value.help_text" class="col-form-label required fw-semibold fs-5" ></span>
                                                <hr class="block mt-6 my-2 opacity-10" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-center mt-10"><a href="javascript:;" class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger" @click="activeTab = 'Confirm'" v-text="translate('Next')"></a></p>
                            </div>

                            <div class="w-full  mx-auto" v-if="activeTab == 'Confirm'" :key="activeTab">
                                
                                <div class="w-full flex gap-10">
                                    <div class="max-w-xl mb-6 mx-auto " >
                                        <hr class="block mt-6 my-2 opacity-10" />
                                        <div class="bg-gray-200 shadow-md mb-10 rounded-xl">
                                            <div class="card-header border-0 pt-9 gap-6">
                                                <div class="card-title m-0 flex  gap-4" v-if="activeItem.title">
                                                    <div class="w-full ">
                                                        <div class="font-semibold" v-text="activeItem.title"></div>
                                                        <div class="" v-text="activeItem.subject"></div>
                                                    </div>
                                                </div>
                                                <label class=" flex gap-2 cursor-pointer">
                                                    <form_field class="flex-end" :item="activeItem"
                                                        :column="fillable.status">
                                                    </form_field>
                                                    <div class="pt-3">
                                                        <span class="badge badge-light fw-bold me-auto px-4 py-3" v-text="!activeItem.status ? 'Pending' : 'Active'"></span>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="card-body p-9" v-if="activeItem">
                                            
                                                <div class="timeline timeline-border-dashed">
                                                
                                                    <div class="timeline-item" v-for="item in ['action', 'subject', 'model', 'receiver_model']">
                                                        <div class="timeline-line"></div>
                                                        <div class="timeline-icon me-4"><vue-feather type="check" class="fs-2 "></vue-feather></div>
                                                        <div class="timeline-content pt-1 mt-n2">
                                                            <div class="overflow-auto pe-3">
                                                                <div class="text-muted me-2 fs-7" v-text="translate(item)"></div>
                                                                <div class="fs-5 fw-semibold mb-2" v-if="activeItem[item]" v-text="activeItem[item]"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div v-if="fillable.template_id" class="timeline-item" v-for="template in fillable.template_id.data">
                                                        <div v-if="template.template_id == activeItem['template_id']" class="timeline-line"></div>
                                                        <div v-if="template.template_id == activeItem['template_id']" class="timeline-icon me-4"><vue-feather type="check" class="fs-2 "></vue-feather></div>
                                                        <div v-if="template.template_id == activeItem['template_id']" class="timeline-content pt-1 mt-n2">
                                                            <div class="overflow-auto pe-3">
                                                                <div class="text-muted me-2 fs-7" v-text="translate('Template')"></div>
                                                                <div class="fs-5 fw-semibold mb-2" v-if="activeItem['template_id']" v-text="template.title"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="h-4px w-100 bg-light mb-5" >
                                                    <div class="rounded h-4px transition transition-all" role="progressbar" :class="progressWidth() < 100 ? 'bg-info' : 'bg-success'" :style="{width: progressWidth()+'%'}"></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <p class="text-center mt-10"><a href="javascript:;" class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger" @click="saveEvent" v-text="translate('Submit')"></a></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>


import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, getCurrentInstance, ref } from 'vue';
import { translate, getProgressWidth, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails,today } from '@/utils.vue';

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);
import tooltip from '@/components/includes/tooltip.vue';

export default
    {
        components: {
            'datatabble': Vue3EasyDataTable,
            delete_icon,
            close_icon,
            form_field,
            tooltip,
        },
        name: 'Taxi trips',
        emits: ['callback'],
        setup(props, { emit }) {

            const loader = ref(false);
            const showAddSide = ref(false);
            const showEditSide = ref(false);
            const showProfilePage = ref(null);
            const activeItem = ref({});
            const activeTab = ref('Info');
            const content = ref({});
            const center = ref({});
            const locations = ref([]);
            const showList = ref(true);
            const searchText = ref('');
            const locationError = ref(null);
            const collapsed = ref(false);
            const tabs = ref(["Info", 'Content', 'Fields',  'Confirm']);
            const places = ref([]);
            const showPlaceSearch = ref(false);
            const pickup_placeSearch = ref('');
            const destination_placeSearch = ref('');
            const tripsStatusList = ref([
                {title: translate("Active"), status: 'on'},
                {title:translate("Pending"), status: 0},
            ]);

            if (props.active_tab) {
                activeTab.value = props.active_tab;
            }
            if (props.item) {
                activeItem.value = props.item
            }

            const saveEvent = () => {
                loader.value = true;
                var params = new URLSearchParams();
                let array = JSON.parse(JSON.stringify(activeItem.value));
                let keys = Object.keys(array)
                let k, d, value = '';
                for (let i = 0; i < keys.length; i++) {
                    k = keys[i]
                    d = typeof array[k] === 'object' ? JSON.stringify(array[k]) : array[k]
                    params.append('params[' + k + ']', d)
                }
                let type = array.id > 0 ? 'update' : 'create';
                params.append('type', 'NotificationEvent.' + type)
                
                const currentInstance =  getCurrentInstance();

                if (currentInstance)
                    currentInstance.root.data.loader = true;
    
                handleRequest(params, '/api/' + type).then(response => {
                    handleAccess(response)
                    loader.value = false;
                })
            }

            
            const back = () => {
                emit('callback');
            }

            const progressWidth = () => 
            {
                let requiredData = ['template_id', 'title', 'receiver_model','status'];
                
                return getProgressWidth(requiredData, activeItem);
            }

            return {
                loader,
                progressWidth,
                content,
                tabs,
                activeItem,
                activeTab,
                translate,
                searchText,
                collapsed,
                saveEvent,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            'item',
            'active_tab',
            'fillable',
        ],

    };
</script>