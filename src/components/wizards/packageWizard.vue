<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">
            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
            <div class=" card w-full py-10">
                <div class="w-full stepper stepper-links ">
                    <div class="stepper-nav justify-content-center py-2 mb-10">
                        <div class="stepper-item " v-for="row in fillable" @click="activeTab = row">
                            <h3 :class="activeTab == row ? 'text-danger border-danger' : 'text-gray-400 border-transparent'"
                                class="cursor-pointer pb-3 px-2 stepper-title text-md border-b " v-text="translate(row)"></h3>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="" v-if="activeTab == 'Info'" :key="activeTab">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="max-w-xl mb-6 mx-auto row">

                                        <input name="params[package_id]" type="hidden">

                                        <label class=" text-xl required font-semibold text-xl" :for="'input' + i"
                                            v-text="translate('Package name')"></label>
                                        <input :required="true" autocomplete="off" name="params[name]"
                                            class="form-control form-control-solid" :placeholder="translate('Package name')"
                                            v-model="activeItem.name">
                                        <hr class="block mt-6 my-2 opacity-10" />
                                        <label class=" text-xl required font-semibold text-xl" :for="'input' + i"
                                            v-text="translate('Description')"></label>
                                        <textarea :required="true" autocomplete="off" name="params[description]"
                                            class="form-control form-control-solid" :placeholder="translate('Description')"
                                            v-model="activeItem.description"> </textarea>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center mt-10"><a href="javascript:;"
                                    class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger"
                                    @click="activeTab = 'Cost'" v-text="translate('Next')"></a></p>
                        </div>

                        
                        <div class="" v-if="activeTab == 'Cost'" :key="activeTab">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="max-w-xl mb-6 mx-auto">

                                        <div class="mx-auto pb-10 flex gap-10">
                                            <p class="w-full flex flex-col">
                                                <span class="text-xl font-semibold w-full" v-text="translate('Package type')"></span>
                                                <span class=" w-full" v-text="translate('check if it is paid Package')"></span>
                                            </p>
                                            <label for="payment-type" class="flex-none flex gap-2 cursor-pointer h-25"  >
                                                <span :class="!activeItem.is_paid ? 'bg-gray-200' : 'bg-red-400'" class="mx-2 mt-1 bg-red-400 block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: activeItem.is_paid ? '16px' : 0}"></a></span>
                                                <span class="badge badge-light fw-bold me-auto px-4 py-3" v-text="!activeItem.is_paid ? 'Free' : 'Paid'"></span>
                                                <input id="payment-type" v-model="activeItem.is_paid" @change="activeItem.payment_type = !activeItem.is_paid ? 'free' : 'paid'"   type="checkbox" class="hidden"  />
                                            </label>

                                        </div>

                                        <div v-if="activeItem.payment_type == 'paid'">
                                            <div 
                                                class="notice d-flex bg-blue-100 rounded border-primary border border-dashed rounded-3 p-6">
                                                <div class="d-flex flex-stack flex-grow-1 ">
                                                    <div class=" font-semibold">
                                                        <h4 class="text-gray-900 fw-bold"
                                                            v-text="translate('Set Plan Cost')"></h4>
                                                        <div class="text-xl text-gray-700 "
                                                            v-text="translate('Set the cost of this package')">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="opacity-10 my-4" />

                                            <div class="w-full mb-6 mx-auto row">
                                                <label class=" text-xl required font-semibold"
                                                    v-text="translate('Cost per month')"></label>
                                                <input :required="true" autocomplete="off" name="params[cost_month]"
                                                    class="form-control form-control-solid"
                                                    :placeholder="translate('Cost per month')" type="number"
                                                    v-model="activeItem.cost_month">
                                            </div>

                                            <div class="w-full mb-6 mx-auto row">
                                                <label class=" text-xl required font-semibold "
                                                    v-text="translate('Cost per quarter')"></label>
                                                <input :required="true" autocomplete="off" name="params[cost_quarter]"
                                                    class="form-control form-control-solid"
                                                    :placeholder="translate('Cost per month')" type="number"
                                                    v-model="activeItem.cost_quarter">
                                            </div>

                                            <div class="w-full mb-6 mx-auto row">
                                                <label class=" text-xl required font-semibold "
                                                    v-text="translate('Cost per year')"></label>
                                                <input :required="true" autocomplete="off" name="params[cost_year]"
                                                    class="form-control form-control-solid"
                                                    :placeholder="translate('Cost per month')" type="number"
                                                    v-model="activeItem.cost_year">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <p class="text-center mt-10"><a href="javascript:;"
                                    class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger"
                                    @click="activeTab = 'Features'" v-text="translate('Next')"></a></p>
                        </div>

                        
                        <div class="" v-if="activeTab == 'Features'" :key="activeTab">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="max-w-xl mb-6 mx-auto">

                                        <div
                                            class="notice d-flex bg-blue-100 rounded border-primary border border-dashed rounded-3 p-6">
                                            <div class="d-flex flex-stack flex-grow-1 ">
                                                <div class=" font-semibold">
                                                    <h4 class="text-gray-900 fw-bold"
                                                        v-text="translate('Set limit of features access')"></h4>
                                                    <div class="text-xl text-gray-700 "
                                                        v-text="translate('Set the access limit for all features and options related to this package')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="opacity-10 my-4" />

                                        <div class="w-full mb-6 mx-auto row" v-for="field in fields">
                                            <label class=" text-xl flex gap-4 required font-semibold text-xl">
                                                <vue-feather :type="field.icon" class="w-6" />
                                                <span class="font-semibold text-xl" v-text="field.title"></span>
                                            </label>
                                            <input :required="field.required" autocomplete="off" 
                                                class="form-control form-control-solid"
                                                :placeholder="field.placeholder" :type="field.column_type"
                                                v-model="activeItem.feature[field.key]">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <p class="text-center mt-10"><a href="javascript:;"
                                    class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger"
                                    @click="activeTab = 'Confirm'" v-text="translate('Next')"></a></p>
                        </div>

                        <div class="w-full  mx-auto" v-if="activeTab == 'Confirm'" :key="activeTab">

                            <div class="max-w-6xl mx-auto">
                                
                                <div class="max-w-xl mx-auto gap-10">
                                    <div class="w-full flex">
                                        <div class="w-full">
                                            <div class="card-header border-0">
                                                <div class="card-title"> <h2 v-text="activeItem.name"></h2> </div>
                                            </div>

                                            <div class="card-body py-0">
                                                <div class="fs-5 font-semibold text-gray-500 mb-4" v-text="activeItem.description"></div>
                                            </div>
                                        </div>
                                        <div class="mx-auto pt-10">

                                            <label class=" flex gap-2 cursor-pointer">
                                                <form_field class="flex-end" :item="activeItem"
                                                    :column="{ key: 'status', title: translate('Status'), column_type: 'checkbox', hide_text:true }">
                                                </form_field>
                                                <div class="pt-3">
                                                    <span class="badge badge-light fw-bold me-auto px-4 py-3" v-text="!activeItem.status ? 'Pending' : 'Active'"></span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="max-w-xl mx-auto gap-10">

                                    <div class="card mb-6 mb-xl-9 bg-inverse-success" v-if="!activeItem.is_paid">
                                        <div class="card-header border-0">
                                            <div class="card-title">
                                                <h2 v-text="translate('Package is free')"></h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-6 mb-xl-9 bg-inverse-success" v-if="activeItem.is_paid">

                                        <div class="card-header border-0">
                                            <div class="card-title">
                                                <h2 v-text="translate('Subscription cost')"></h2>
                                            </div>
                                        </div>

                                        <div class="card-body py-0">
                                            <div class="fs-5 font-semibold text-gray-500 mb-4"
                                                v-text="translate('Costs for Package subscription based on duration')"></div>

                                            <div class="d-flex flex-wrap flex-stack mb-5">
                                                <div class="d-flex ">
                                                    <div
                                                        class="border border-dashed border-gray-300 w-150px rounded my-3 p-4 me-6">
                                                        <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                            <span v-text="'$'"></span><span class="counted"
                                                                v-text="activeItem.cost_month"></span>
                                                            <span class="text-xl font-semibold text-muted d-block lh-1 pt-2"
                                                                v-text="translate('/Month')"></span>
                                                        </span>
                                                    </div>

                                                    <div
                                                        class="border border-dashed border-gray-300 w-150px rounded my-3 p-4 me-6">
                                                        <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                            <span v-text="'$'"></span><span class="counted"
                                                                v-text="activeItem.cost_quarter"></span>
                                                            <span class="text-xl font-semibold text-muted d-block lh-1 pt-2"
                                                                v-text="translate('/Quarter')"></span>
                                                        </span>

                                                    </div>

                                                    <div
                                                        class="border border-dashed border-gray-300 w-150px rounded my-3 p-4 me-6">
                                                        <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                            <span v-text="'$'"></span><span class="counted"
                                                                v-text="activeItem.cost_year"></span>
                                                            <span class="text-xl font-semibold text-muted d-block lh-1 pt-2"
                                                                v-text="translate('/Year')"></span>
                                                        </span>

                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                  
                                </div>
                            </div>
                            <p class="text-center mt-10"><a href="javascript:;"
                                    class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger"
                                    @click="savePackage" v-text="translate('Submit')"></a></p>
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

import { defineAsyncComponent, ref } from 'vue';
import { translate, getProgressWidth, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-update.vue')
);

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);

export default
    {
        components: {
            SideFormCreate,
            SideFormUpdate,
            close_icon,
            delete_icon,
            form_field,
        },
        name: 'Packages',
        emits: ['callback'],
        setup(props, { emit }) {

            const showEditSide = ref(false);
            const activeItem = ref({feature: {}});
            const activeTab = ref('Info');
            const content = ref({});
            const fillable = ref(['Info', 'Cost' ,'Features', 'Confirm']);

            if (props.item) {
                activeItem.value = props.item
            }

            const savePackage = () => {
                var params = new URLSearchParams();
                let array = JSON.parse(JSON.stringify(activeItem.value));
                let keys = Object.keys(array)
                let k, d, value = '';
                for (let i = 0; i < keys.length; i++) {
                    k = keys[i]
                    d = typeof array[k] === 'object' ? JSON.stringify(array[k]) : array[k]
                    params.append('params[' + k + ']', d)
                }

                let type = array.package_id > 0 ? 'update' : 'create';
                params.append('type', 'Package.' + type)
                handleRequest(params, '/api/' + type).then(response => {
                    handleAccess(response)
                })
            }

            const back = () => {
                emit('callback');
            }


            const progressWidth = () => {
                let requiredData = ['name', 'description',  'double_cost_month', 'double_cost_quarter', 'double_cost_year', 'status'];

                return getProgressWidth(requiredData, activeItem);
            }

            return {
                progressWidth,
                showEditSide,
                content,
                fillable,
                activeItem,
                activeTab,
                translate,
                savePackage,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            'setting',
            'item',
            'fields',
            'currency'
        ],

    };
</script>