<template>
    <div class="d-flex flex-column flex-column-fluid">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content  flex-column-fluid ">

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container  container-xxl relative">
                <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />

                <!--begin::Invoice 2 main-->
                <div class="card">
                    <!--begin::Body-->
                    <div class="card-body py-lg-20">
                        <!--begin::Layout-->
                        <div class="d-flex flex-column flex-xl-row">
                            <!--begin::Content-->
                            <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                                <div class="mt-n1">
                                    <div class="d-flex flex-stack pb-10" >
                                        <a href="#" >
                                            <img alt="Logo" class="w-40" :src="system_setting.logo">
                                        </a>
                                    </div>
                                    <div class="m-0">
                                        
                                        <div class="fw-bold fs-3 text-gray-800 mb-8" v-text="translate('Invoice')+' #'+activeItem.code"> </div>
                                        <div class="row g-5 mb-11">
                                            <div class="col-sm-6">
                                                
                                                <div class="fw-semibold fs-3 text-gray-600 mb-1" v-text="translate('Issue Date')"></div>
                                                

                                                <!--end::Col-->
                                                <div class="fw-bold fs-3 text-gray-800" v-text="activeItem.date"></div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Col-->

                                            <!--end::Col-->
                                            <div class="col-sm-6">
                                                
                                                <div class="fw-semibold fs-3 text-gray-600 mb-1" v-text="translate('Due Date')"></div>
                                                

                                                <!--end::Info-->
                                                <div class="fw-bold fs-3 text-gray-800 d-flex align-items-center flex-wrap">
                                                    <span class="pe-2" v-text="activeItem.date"></span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->

                                        <!--begin::Row-->
                                        <div class="row g-5 mb-12">
                                            <!--end::Col-->
                                            <div class="col-sm-6" v-if="activeItem.user">
                                                
                                                <div class="fw-semibold fs-3 text-gray-600 mb-1" v-text="translate('Issue For')"></div>
                                                
                                                <!--end::Text-->
                                                <div class="flex gap-2" >
                                                    <img :src="activeItem.user.picture" class="w-10 h-10" />
                                                    <div class="fw-bold fs-3 text-gray-800">
                                                        <div class="text-gray-800" v-text="activeItem.user.name"></div>
                                                        <div class="text-gray-800 fs-normal" v-text="activeItem.user.mobile"></div>
                                                    </div>
                                                </div>
                                                <!--end::Text-->

                                                <!--end::Description-->
                                                <div class="fw-semibold fs-3 text-gray-600" >
                                                    <span v-text="activeItem.user.field.address"></span>
                                                    <span v-text="activeItem.user.field.country"></span>
                                                </div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Col-->

                                            <!--end::Col-->
                                            <div class="col-sm-6">
                                                
                                                <div class="fw-semibold fs-3 text-gray-600 mb-1" v-text="translate('Issued By')"></div>
                                                

                                                <!--end::Text-->
                                                <div class="flex gap-2" v-if="system_setting ">
                                                    <img :src="system_setting.logo" class="w-10 h-10" />
                                                    <div class="fw-bold fs-3 text-gray-800">
                                                        <div class="text-gray-800" v-text="system_setting.sitename"></div>
                                                    </div>
                                                </div>

                                                <!--end::Description-->
                                                <div class="fw-semibold fs-3 text-gray-600"  v-if="system_setting" v-text="system_setting.address"></div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->


                                        <!--begin::Content-->
                                        <div class="flex-grow-1">
                                            <!--begin::Table-->
                                            <div class="table-responsive border-bottom mb-9">
                                                <table class="table mb-3">
                                                    <thead>
                                                        <tr class="border-bottom fs-3 fw-bold text-muted">
                                                            <th class="min-w-175px pb-2" v-text="translate('Description')"></th>
                                                            <th class="min-w-70px text-end pb-2" v-text="translate('Subtotal')"></th>
                                                            <th class="min-w-70px text-end pb-2" v-text="translate('Qty')"></th>
                                                            <th class="min-w-80px text-end pb-2"  v-text="translate('Discount')"></th>
                                                            <th class="min-w-100px text-end pb-2"  v-text="translate('Total')"></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody v-if="activeItem.item">
                                                        <tr class="fw-bold text-gray-700 fs-5 text-end" >
                                                            <td class="d-flex align-items-center pt-6 gap-2"  >
                                                                <vue-feather type="cloud-lightning" ></vue-feather>
                                                                <a href="javascript:;" v-if="activeItem.item.package" v-text="activeItem.item.package.name"></a>
                                                            </td>
                                                            <td class="pt-6" v-text="'$' + activeItem.subtotal"></td>
                                                            <td class="pt-6" v-text="activeItem.duration"></td>
                                                            <td class="pt-6" v-text="'$' + activeItem.discount_amount"></td>
                                                            <td class="pt-6 text-gray-900 fw-bolder" v-text="'$' + activeItem.total_amount "></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--end::Table-->

                                            <!--begin::Container-->
                                            <div class="d-flex justify-content-end">
                                                <!--begin::Section-->
                                                <div class="mw-300px">
                                                    <div class="d-flex flex-stack mb-3">
                                                        <div class="fw-semibold pe-10 text-gray-600 fs-3" v-text="translate('Subtotal')"></div>
                                                        <div class="text-end fw-bold fs-3 text-gray-800" v-text="'$' + activeItem.subtotal "></div>
                                                    </div>
                                                        
                                                    <div class="d-flex flex-stack mb-3">
                                                        <div class="fw-semibold pe-10 text-gray-600 fs-3" v-text="translate('Discount')"></div>
                                                        <div class="text-end fw-bold fs-3 text-gray-800" v-text="'$' + activeItem.discount_amount"></div>
                                                    </div>
                            
                                                    <div class="d-flex flex-stack mb-3">
                                                        <div class="fw-semibold pe-10 text-gray-600 fs-3" v-text="translate('Total amount')"></div>
                                                        <div class="text-end fw-bold fs-3 text-gray-800" v-text="'$' + activeItem.total_amount"></div>
                                                    </div>
                            
                                                </div>
                                                <!--end::Section-->
                                            </div>
                                            <!--end::Container-->
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Invoice 2 content-->
                            </div>
                            <!--end::Content-->

                            <!--begin::Sidebar-->
                            <div class="m-0">
                                <!--begin::Invoice 2 sidebar-->
                                <div v-if="activeItem.transaction"
                                    class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                                    <!--begin::Labels-->
                                    <div class="mb-8">
                                        <span class="badge badge-light-success me-2" v-text="activeItem.status"></span>
                                    </div>
                                    <!--end::Labels-->

                                    <!--begin::Title-->
                                    <h6 class="mb-8 fw-bolder text-gray-600 text-hover-primary" v-text="translate('PAYMENT DETAILS')"></h6>
                                    <!--end::Title-->

                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('payment method')"></div>

                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.transaction.payment_method"></div>
                                    </div>
            
                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Transaction number')"></div>

                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.transaction.transaction_id"></div>
                                    </div>
            
                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Total amount')"></div>

                                        <div class="fw-bold text-gray-800 fs-3" v-text="'$' + activeItem.transaction.amount"></div>
                                    </div>
                                    
                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Status')"></div>

                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.transaction.status"></div>
                                    </div>
            
                                    
                                    <h6 class="mb-8 fw-bolder text-gray-600 text-hover-primary" v-text="translate('Billing info')"></h6>

                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Payer name')"></div>
                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.customer.name"></div>
                                    </div>

                                    <!--begin::Item-->
                                    <div class="mb-6">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Payer email')"></div>

                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.customer.email"></div>
                                    </div>
                                    
                                    <div class="m-0">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Code')"></div>
                                        
                                        <div class="fw-bold text-gray-800 fs-3" v-text="activeItem.code"></div>
                                    </div>
                                    
                                    <div class="m-0">
                                        <div class="fw-semibold text-gray-600 fs-3" v-text="translate('Date')"></div>

                                        <div class="fw-bold fs-3 text-gray-800 d-flex align-items-center" v-text="activeItem.date"> 
                                        </div>
                                    </div>
            
                                </div>
                                <!--end::Invoice 2 sidebar-->
                            </div>
                            <!--end::Sidebar-->
                        </div>
                        <!--end::Layout-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Invoice 2 main-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->

    </div>
</template>
<script>

import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';
import route_icon from '@/components/svgs/route.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, ref } from 'vue';
import { translate, getProgressWidth, durationMonthsDate, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';

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
            'datatabble': Vue3EasyDataTable,
            SideFormCreate,
            SideFormUpdate,
            close_icon,
            delete_icon,
            car_icon,
            route_icon,
            form_field,
        },
        name: 'PackageSubscriptions',
        emits: ['callback'],
        setup(props, { emit }) {

            const showEditSide = ref(false);
            const activeItem = ref({});
            const activeTab = ref(props.usertype);
            const content = ref({});
            const fillable = ref([props.usertype, 'Package', 'Subscription', 'Confirm']);
            const searchText = ref('');

            if (props.item) {
                activeItem.value = props.item
            }

            const savePackageSubscription = () => {
                var params = new URLSearchParams();
                let array = JSON.parse(JSON.stringify(activeItem.value));
                let keys = Object.keys(array)
                let k, d, value = '';
                for (let i = 0; i < keys.length; i++) {
                    k = keys[i]
                    d = typeof array[k] === 'object' ? JSON.stringify(array[k]) : array[k]
                    params.append('params[' + k + ']', d)
                }

                let type = array.subscription_id > 0 ? 'update' : 'create';
                params.append('type', 'PackageSubscription.' + type)
                handleRequest(params, '/api/' + type).then(response => {
                    handleAccess(response)
                })
            }

            const back = () => {
                emit('callback');
            }


            const progressWidth = () => {
                let requiredData = ['model_id', 'package_id', 'start_date', 'payment_type', 'daily_trips', 'payment_status'];

                return getProgressWidth(requiredData, activeItem);
            }

            const checkSimilarUser = (item) => {
                let name = (item.name).toLowerCase().includes(searchText.value.toLowerCase()) ? true : false;
                let email = name ? name : (item.mobile).toLowerCase().includes(searchText.value.toLowerCase()) ? true : false;
                return email ? email : ((item.parent.name).toLowerCase().includes(searchText.value.toLowerCase()) ? true : false);
            }

            const setUser = (model) => {
                activeItem.value.model_id = model.customer_id;
                activeItem.value.model = model;
                activeItem.value.user_type = props.usertype;
                activeTab.value = 'Package';
                searchText.value = null;
            }

            const findUser = () => {
                for (let i = 0; i < props.userslist.length; i++) {
                    props.userslist[i].show = searchText.value.trim() ? checkSimilarUser(props.userslist[i]) : 1;
                }
            }


            const checkSimilarPackage = (item) => {
                let name = (item.name).toLowerCase().includes(searchText.value.toLowerCase()) ? true : false;
                return name ? name : ((item.description).toLowerCase().includes(searchText.value.toLowerCase()) ? true : false);
            }

            const setPackage = (packageItem) => {
                activeItem.value.package_id = packageItem.package_id;
                activeItem.value.package = packageItem;
                activeTab.value = 'Subscription';
                searchText.value = null;
            }

            const findPackage = () => {
                if (props.packages) {
                    for (let i = 0; i < props.packages.length; i++) {
                        props.packages[i].show = searchText.value.trim() ? checkSimilarPackage(props.packages[i]) : 1;
                    }
                }
            }

            const setType = (val) => {
                activeItem.value.payment_type = val;
                dateChanged()
            }

            const dateChanged = () => {

                if (!activeItem.value.start_date)
                    return null;

                let value = 0;
                if (activeItem.value.payment_type == 'month')
                    value = 1;

                if (activeItem.value.payment_type == 'quarter')
                    value = 3;

                if (activeItem.value.payment_type == 'year')
                    value = 12;

                activeItem.value.end_date = durationMonthsDate(activeItem.value.start_date, value);
            }

            const showTip = ref(false);

            const totalCost = () => {

                let priceType = (activeItem.value.daily_trips == 1) ? ('single_cost_' + activeItem.value.payment_type) : ('double_cost_' + activeItem.value.payment_type);

                activeItem.value.total_cost = activeItem.value.package[priceType];

                return activeItem.value.total_cost;
            }


            return {
                totalCost,
                showTip,
                dateChanged,
                setType,
                findPackage,
                checkSimilarPackage,
                setPackage,
                findUser,
                setUser,
                checkSimilarUser,
                progressWidth,
                showEditSide,
                content,
                fillable,
                activeItem,
                activeTab,
                translate,
                savePackageSubscription,
                searchText,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            
            'item',
            'userslist',
            'usertype',
            'packages',
            'currency'
        ],

    };
</script>