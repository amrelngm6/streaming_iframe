<template>
    <div class="mx-auto w-full py-8">
        <div class=" absolute top-0 w-full h-full left-0 bg-black " v-if="showLoader" style="z-index: 9999;opacity:.5"></div>

        <h2 class="text-xl" v-text="translate('get_started_setting')"></h2>
        <p class="py-4 " v-text="translate('complete_this_settings_to_start')"></p>
        <div class="mx-auto  rounded-lg py-8 lg:flex gap-6" v-if="steps">
            <div
                class="card d-flex justify-content-center justify-content-xl-start flex-row-auto w-100 w-xl-300px w-xxl-400px">
                <div class="card-body px-6 px-xxl-15 py-20">
                    <div class="stepper-nav">
                        <div class="stepper-item cursor-pointer" data-kt-stepper-element="nav" v-for="step in steps" :key="step.active">
                            <div class="stepper-wrapper flex gap-6 " :class="step.active ? '' : 'text-gray-300'"
                                @click="setActiveStep(step.id)">
                                <div class="stepper-icon w-40px h-40px rounded-lg  text-center bg-inverse-primary   "
                                    :class="step.active ? 'bg-primary' : ''">
                                    <span class="stepper-number font-bold text-2xl block pt-2" v-text="step.id"></span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title" :class="step.id == activeStep ? 'font-bold' : 'font-medium'"
                                        v-text="step.title"> </h3>
                                    <div class="stepper-desc fw-semibold" v-text="step.info"></div>
                                </div>
                            </div>
                            <div class="stepper-line h-40px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-2/3  bg-white py-20 px-6 rounded shadow-md">
                <div class="w-full" id="step1" v-if="activeStep == 1">
                    <p class="text-lg font-semibold" v-text="translate('Business information')"></p>
                    <p class=" font-normal" v-text="translate('information about the business')"></p>
                    <hr class="py-2 mt-4" />
                    <div class="relative py-4"><label class="nui-label w-full pb-2 block font-semibold text-lg"
                            for="ninja-input-89" v-text="translate('Business name')"></label>
                        <div class="group/nui-input relative">
                            <input :disabled="auth && auth.business && auth.business.business_name ? true : false"
                                id="ninja-input-89" type="text" class="form-control form-control-solid"
                                placeholder="Ex: Medians Trips" v-model="activeItem.business_name">
                        </div>
                    </div>
                    <div class="relative py-4"><label class="nui-label w-full pb-2 block font-semibold text-lg"
                            for="ninja-input-89" v-text="translate('Business type')"></label>
                        <div class="row">
                            <!--begin::Col-->
                            <div class="col-lg-6" @click="setBusinessType('school')">
                                <!--begin::Option-->
                                <input type="radio" class="btn-check" value="school"
                                    :checked="activeItem.business_type == 'school' ? true : false" name="business_type"
                                    id="school" />
                                <label
                                    class="gap-6 btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-10"
                                    for="kt_create_account_form_account_type_personal">
                                    <vue-feather type="book-open"></vue-feather>
                                    <!--begin::Info-->
                                    <span class="d-block fw-semibold text-start">
                                        <span class="text-gray-900 fw-bold d-block fs-4 mb-2"
                                            v-text="translate('School')"></span>
                                        <span class="text-muted fw-semibold fs-6"
                                            v-text="translate('For School students transportation services')"></span>
                                    </span>
                                    <!--end::Info-->
                                </label>
                                <!--end::Option-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-lg-6" @click="setBusinessType('company')">
                                <!--begin::Option-->
                                <input type="radio" class="btn-check" value="company"
                                    :checked="activeItem.business_type == 'company' ? true : false" name="business_type"
                                    id="company" />
                                <label
                                    class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center"
                                    for="kt_create_account_form_account_type_corporate">
                                    <vue-feather type="briefcase"> </vue-feather>
                                    <i class="ki-duotone ki-briefcase fs-3x me-5"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    <!--begin::Info-->
                                    <span class="d-block fw-semibold text-start">
                                        <span class="text-gray-900 fw-bold d-block fs-4 mb-2"
                                            v-text="translate('Company')"></span>
                                        <span class="text-muted fw-semibold fs-6"
                                            v-text="translate('For Companies employees transportation services')"></span>
                                    </span>
                                    <!--end::Info-->
                                </label>
                                <!--end::Option-->
                            </div>
                            <!--end::Col-->
                        </div>

                    </div>
                    <div class="block  w-full">
                        <div class="mx-auto w-40 flex flex-end items-center gap-4 py-6">
                            <!-- <button type="button" class="is-button rounded-lg is-button-default !h-12 w-full">Back</button> -->
                            <button type="button" @click="saveBusiness()"
                                class="is-button rounded-lg bg-purple-800 text-white h-12 w-full "
                                v-text="translate('Next')"></button>
                        </div>
                    </div>
                </div>
                <div class="w-full" id="step2" v-if="activeStep == 2">
                    <p class="text-lg font-semibold" v-text="translate('plan')"></p>
                    <p class=" font-normal" v-text="translate('Subscribe to plan')"> </p>
                    <hr class="py-2 mt-4" />
                    <div class="block  w-full" v-if="content.plans">

                        <div class="d-flex flex-column">
                            <!--begin::Nav group-->
                            <div class="nav-group nav-group-outline mx-auto" data-kt-buttons="true" data-kt-initialized="1">
                                <button v-for="priceItem in pricesList" class="btn btn-color-gray-500 btn-active btn-active-secondary px-6 py-3 me-2 "
                                    :class="activePrice == priceItem ? 'active' : ''"
                                    @click="switchPlanPrice(priceItem)"
                                    v-text="priceItem">
                                </button>
                            </div>
                            <!--end::Nav group-->

                            <!--begin::Row-->
                            <div class="row mt-10">
                                <!--begin::Col-->
                                <div class="col-lg-6 mb-10 mb-lg-0">
                                    <!--begin::Tabs-->
                                    <div class="nav flex-column" role="tablist">
                                        <!--begin::Tab link-->
                                        <label  v-for="plan in content.plans"
                                            class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6  mb-6"
                                            :class="activePlan.plan_id == plan.plan_id ? 'active' : '' "
                                            >

                                            <!--end::Description-->
                                            <div  @click="setActivePlan(plan)" class="d-flex align-items-center me-2">
                                                <!--begin::Radio-->
                                                <div
                                                    class="form-check form-check-custom form-check-solid form-check-success flex-shrink-0 me-6">
                                                    <input class="form-check-input" type="radio" name="plan"
                                                        :checked="activePlan.plan_id == plan.plan_id" value="startup">
                                                </div>
                                                <!--end::Radio-->

                                                <!--begin::Info-->
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center fs-2 fw-bold flex-wrap" v-text="plan.name">
                                                    </div>
                                                    <div class="fw-semibold opacity-75" v-text="plan.type"></div>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::Description-->

                                            <!--begin::Price-->
                                            <div class="ms-5">
                                                <span class="mb-2"><span v-text="currency.symbol"></span></span>

                                                <span class="fs-3x fw-bold" v-text="planCost(plan)"></span>

                                                <span class="fs-7 opacity-50">
                                                    / <span data-kt-element="period" v-text="activePrice"></span>
                                                </span>
                                            </div>
                                            <!--end::Price-->
                                        </label>
                                    </div>
                                    <!--end::Tabs-->
                                </div>
                                <!--end::Col-->

                                <!--begin::Col-->
                                <div class="col-lg-6">
                                    <!--begin::Tab content-->
                                    <div class="tab-content rounded h-100 bg-light p-10" >
                                        <!--begin::Tab Pane-->
                                        <div class="tab-pane fade show active" >
                                            <!--begin::Heading-->
                                            <div class="pb-5">
                                                <h2 class="fw-bold text-gray-900" v-text="translate('Plan features')"></h2>

                                                <div class="text-muted fw-semibold" v-text="translate('Some features are available only for Paid plans')">
                                                    
                                                </div>
                                            </div>
                                            <!--end::Heading-->

                                            <!--begin::Body-->
                                            <div class="pt-1">
                                                <!--begin::Item-->
                                                <div class="d-flex align-items-center mb-7"  v-for="(feature, index) in activePlan.plan_features">
                                                    <div class="gap-2 flex">
                                                        <span v-html="feature.access > 0 ? (feature.type == 'boolen' ? '✔️' : feature.access) : '&#10006;'" class="text-purple-500 font-semibold"></span>
                                                        <span v-text="feature.code"></span>
                                                    </div>
                                                    
                                                </div>
                                                <!--end::Item-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Tab Pane-->
                                    </div>
                                    <!--end::Tab content-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>

                        <div class="block  w-full">
                            <div class="mx-auto w-40 flex flex-end items-center gap-4 py-6">
                                <!-- <button type="button" class="is-button rounded-lg is-button-default !h-12 w-full">Back</button> -->
                                <button type="button" @click="activeStep = 3"
                                    class="is-button rounded-lg bg-purple-800 text-white h-12 w-full "
                                    v-text="translate('Next')"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full" id="step3" v-if="activeStep == 3">
                    <p class="text-lg font-semibold" v-text="translate('Review and confirm')"></p>
                    <p class=" font-normal" v-text="translate('Review your information and confirm')"> </p>
                    <hr class="py-2 mt-4" />
                    <div class="block  w-full">
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="payment__success__inner">
                                    <div class="payment__success__header">
                                        <h2 v-text="translate('Confirmation')"></h2>
                                    </div>
                                    <div class="relative bg-white mt-4 p-5 py-10 sm:rounded-3xl sm:px-10">
                                        <div class="w-full">
                                            <h2 class="text-base font-semibold leading-7 text-slate-900"
                                                v-text="activePlan.name"></h2>
                                            <p class="text-lg leading-6 text-slate-700"></p>
                                        </div>
                                        <h3 class="sr-only">All-access features</h3>
                                        <ul class="mt-8 space-y-8 text-lg leading-6 text-slate-700">
                                            <li class="flex py-2">
                                                <p class="w-full ml-6"><strong class="font-semibold text-slate-900"
                                                        v-text="translate('Start date')"></strong>— <span
                                                        v-text="today()"></span></p>
                                                <p class="w-full ml-6"><strong class="font-semibold text-slate-900"
                                                        v-text="translate('End date')"></strong>— <span
                                                        v-text="endDate()"></span></p>
                                            </li>
                                            <li class="flex py-2" v-if="activePlan.type == 'paid'">
                                                <p class="w-full ml-6"><strong class="font-semibold text-slate-900"
                                                        v-text="translate('Payment method')"></strong></p>
                                                <ul class="flex nav-pills nav-pills-custom mb-3">
                                                    <li class="nav-item mb-3 me-3 me-lg-6" role="presentation" v-if="setting.paypal_payment">
                                                        <a @click="paymentMethod = 'paypal'"  :class="paymentMethod == 'paypal' ? 'border-black' : ''"  class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2 " id="kt_stats_widget_16_tab_link_2"  href="javascript:;" >
                                                            <div class="nav-icon mb-3">        
                                                                <svg width="30px"
                                                                    height="30px" viewBox="-3.5 0 48 48" version="1.1"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                                                    <title>Paypal-color</title>
                                                                    <desc>Created with Sketch.</desc>
                                                                    <defs> </defs>
                                                                    <g id="Icons" stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <g id="Color-" transform="translate(-804.000000, -660.000000)"
                                                                            fill="#022B87">
                                                                            <path
                                                                                d="M838.91167,663.619443 C836.67088,661.085983 832.621734,660 827.440097,660 L812.404732,660 C811.344818,660 810.443663,660.764988 810.277343,661.801472 L804.016136,701.193856 C803.892151,701.970844 804.498465,702.674333 805.292267,702.674333 L814.574458,702.674333 L816.905967,688.004562 L816.833391,688.463555 C816.999712,687.427071 817.894818,686.662083 818.95322,686.662083 L823.363735,686.662083 C832.030541,686.662083 838.814901,683.170138 840.797138,673.069296 C840.856106,672.7693 840.951363,672.194809 840.951363,672.194809 C841.513828,668.456868 840.946827,665.920407 838.91167,663.619443 Z M843.301017,674.10803 C841.144899,684.052874 834.27133,689.316292 823.363735,689.316292 L819.408334,689.316292 L816.458414,708 L822.873846,708 C823.800704,708 824.588458,707.33101 824.733611,706.423525 L824.809211,706.027531 L826.284927,696.754676 L826.380183,696.243184 C826.523823,695.335698 827.313089,694.666708 828.238435,694.666708 L829.410238,694.666708 C836.989913,694.666708 842.92604,691.611256 844.660308,682.776394 C845.35583,679.23045 845.021677,676.257496 843.301017,674.10803 Z"
                                                                                id="Paypal"> </path>
                                                                        </g>
                                                                    </g>
                                                                </svg>
                                                            </div>
                                                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1" v-text="translate('PayPal')"> </span> 
                                                        </a>
                                                    </li>
                                                    <li class="nav-item mb-3 me-3 me-lg-6" role="presentation" v-if="setting.paystack">
                                                        <a @click="paymentMethod = 'paystack'" :class="paymentMethod == 'paystack' ? 'border-black' : ''"  class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden w-80px h-85px pt-5 pb-2 " id="kt_stats_widget_16_tab_link_2" href="javascript:;" >
                                                            <div class="nav-icon mb-3">        
                                                                <vue-feather type="credit-card" />
                                                            </div>
                                                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1" v-text="translate('PayStack')"> </span> 
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <div class="relative -mx-5 mt-8 ring-1 ring-slate-900/5 sm:mx-0 sm:rounded-2xl">
                                            <div class="relative flex flex-col bg-slate-50 px-5 py-8 sm:rounded-2xl">
                                                <p class="flex items-center justify-center gap-6"><span
                                                        class="text-[2.5rem] leading-none text-slate-900"><span
                                                            v-text="currency ? currency.symbol : ''"></span><span class="font-bold"
                                                            v-text="cost()"></span></span><span class="ml-3 text-lg"><span
                                                            class="font-semibold text-slate-900"
                                                            v-text="translate('yearly payment')"></span><br><span
                                                            class="text-slate-500"
                                                            v-text="translate('include local taxes')"></span></span></p>
                                                <p
                                                    class="order-last -mx-1 mt-4 flex justify-center text-lg leading-6 text-slate-500 sm:space-x-2">
                                                    <span class="sm:hidden"
                                                        v-text="translate('Includes free updates and technical support')"></span>
                                                </p>
                                                <div  v-if="activePlan.type == 'free'" >
                                                    <a @click="complete()" 
                                                        class="inline-flex justify-center rounded-lg text-lg font-semibold py-4 px-3 bg-slate-900 text-white hover:bg-slate-700 mt-6 w-full"
                                                        href="javascript:;"><span v-text="translate('Complete')"></span></a></div>
                                                        
                                                <div id="paypal-button-container" v-if="activePlan.type != 'free'">
                                                    <a @click="complete()" v-if="setting.paypal_payment && paymentMethod == 'paypal'"
                                                        class="inline-flex justify-center rounded-lg text-lg font-semibold py-4 px-3 bg-slate-900 text-white hover:bg-slate-700 mt-6 w-full"
                                                        href="javascript:;"><span v-text="translate('Pay with PayPal')"></span></a>
                                                    <a @click="complete()" v-if="setting.paystack_payment && paymentMethod == 'paystack'"
                                                        class="inline-flex justify-center rounded-lg text-lg font-semibold py-4 px-3 bg-info text-white hover:bg-slate-700 mt-6 w-full"
                                                        href="javascript:;"><span v-text="translate('Pay with PayStack')"></span></a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import moment from 'moment';
import { onMounted, ref } from 'vue';
import { translate, handleGetRequest, handleRequest, deleteByKey, showAlert } from '@/utils.vue';
import VueScript2 from 'vue-script2';
import { loadScript } from "@paypal/paypal-js";
import axios from 'axios'
import CurrencyAPI from '@everapi/currencyapi-js';

export default
    {
        components: {
            moment,
            VueScript2,
            translate,
            showAlert
        },

        setup(props) {

            const showLoader = ref(false);
            const showEditSide = ref(false);
            const activePlan = ref(false);
            const activePrice = ref('yearly');
            const paymentMethod = ref('paypal');
            const paypalScriptLoaded = ref(false);
            const currencyConverted = ref({});
            const currencyApi = new CurrencyAPI(props.setting.currency_converter_api);
            

            /**
             * Check default step
             */
            const handleActiveStep = () => {
                if ((props.auth && !props.auth.business))
                    return 2
            }

            let steps = [{ id: 1, title: translate('Business information'), info: translate('information about the business'), active: 1 },
            { id: 2, title: translate('Plan'), info: translate('Subscribe to plan'), active: (handleActiveStep() > 2) ? 1 : 0 },
            { id: 3, title: translate('Review and confirm'), info: translate('Review your information and confirm'), active: 0 }];

            const pricesList = ['monthly', 'yearly'];


            const url = props.conf.url + 'admin/get_started?load=json';

            const content = ref({
                title: '',
                items: [],
                columns: [],
            });

            const activeItem = ref({});

            const activeStep = ref(null);

            function load() {
                handleGetRequest(url).then(response => {
                    content.value = JSON.parse(JSON.stringify(response))
                    activePlan.value = content.value.plans[1]

                });
            }

            load();

            function closeSide(data) {
                showEditSide.value = false;
            }

            /**
             * Handle actions from datatable buttons
             * Called From 'dataTableActions' component
             * 
             * @param String actionName 
             * @param Object data
             */
            function handleAction(actionName, data) {
                switch (actionName) {
                    case 'edit':
                        activeItem.value = data;
                        showEditSide.value = true;
                        break;

                    case 'delete':
                        deleteByKey(props.object_key, data, props.object_name + '.delete');
                        break;
                }
            }

            const validateStep = (id) => {
                if (id == 1 && !activeItem.value.business_name)
                    return translate('business_name_required')

                if (id == 1 && !activeItem.value.business_type)
                    return translate('Business type required')

                if (id == 3 && !activePlan.value)
                    return translate('Select plan first')

            }

            /**
             * Get Today date
             * 
             */
            const today = () => {
                return moment().format('YYYY-MM-DD')
            }

            /**
             * Get end Date based on plan
             */
            const endDate = () => {
                let days = activePrice.value == 'monthly' ? 30 : 365;

                return moment().add(days, 'days').format('YYYY-MM-DD')

            }


            /**
             * Switch the plan price between
             * ( Monthly - Yearly )
             */
            const switchPlanPrice = (type) => {
                activePrice.value = type;
            }



            /**
             * Cost of current plan
             */
             const cost = () => {
                let totalcost = activePrice.value == 'monthly' ? activePlan.value.monthly_cost : activePlan.value.yearly_cost;
                return totalcost ? totalcost.toFixed(2) : '0.00'
            }

            
            /**
             * Cost of current plan
             */
             const planCost = (plan) => {
                let totalcost = activePrice.value == 'monthly' ? plan.monthly_cost : plan.yearly_cost;
                return totalcost ? totalcost.toFixed(2) : '0.00'
            }




            /**
             * Set between type
             */
            const setBusinessType = (type) => {
                activeItem.value.business_type = type;
            }

            /**
             * Set between name
             */
            const setBusinessName = (name) => {
                activeItem.value.business_name = name;
            }


            /**
             * Switch between steps
             */
            const setActiveStep = (id) => {

                for (var i = steps.length - 1; i >= 0; i--) {
                    let s = steps[i];
                    let p = steps[i - 1];
                    if (i && s.id == id && p && p.active && s.id) {
                        steps[i].active = true
                    }

                    if (s && s.active && s.id == id) {

                        activeStep.value = s.id
                    }
                }
            }

            setActiveStep((props.auth && props.auth.business) ? 2 : 1);

            setBusinessType((props.auth && props.auth.business) ? props.auth.business.type : null);

            setBusinessName((props.auth && props.auth.business) ? props.auth.business.business_name : null);

            /**
             * Save business name
             */
            const saveBusiness = () => {

                if (validateStep(1))
                    return showAlert(validateStep(1));

                showLoader.value = true;

                const params = new URLSearchParams([]);
                params.append('type', 'User.get_started_save_business');
                params.append('params[business_name]', activeItem.value.business_name);
                params.append('params[type]', activeItem.value.business_type);
                handleRequest(params, '/api/create').then(data => {
                    showLoader.value = false;
                    data.error ? showAlert(data.error) : ''
                    data.success ? setActiveStep(2) : ''
                });
            }

            /**
             * Complete and go start
             */
            const complete = () => {

                if (!activePlan.value.plan_id)
                    return showAlert(translate('Select plan first'));


                if (cost() < 1) {
                    showLoader.value = true;
                    const params = new URLSearchParams([]);
                    params.append('type', 'User.get_started_save_free_plan');
                    params.append('params[plan_id]', activePlan.value.plan_id);
                    params.append('params[payment_type]', activePrice.value);
                    handleRequest(params, '/api/create').then(data => {
                        showLoader.value = false;
                        if (data.success) {
                            showAlert(data.result)
                            setTimeout(() => {
                                window.location.reload()
                            }, 2000);
                        } else {
                            showAlert(data.error)
                        }
                    });

                    return true;
                }


                if (paymentMethod.value == 'paypal')
                {
                    completePayPal();
                }
                
                if (paymentMethod.value == 'paystack')
                {
                    completePaystack();
                }
            }


            const completePayPal = () => {
                showLoader.value = true;

                const params = new URLSearchParams([]);
                params.append('type', 'User.get_started_save_plan');
                params.append('params[plan_id]', activePlan.value.plan_id);
                params.append('params[payment_type]', activePrice.value);
                params.append('params[payment_method]', paymentMethod.value);
                handleRequest(params, '/api/create').then(data => {
                    // Load the PayPal SDK dynamically
                    loadScript({
                        clientId: props.setting.paypal_api_key,
                        commit: true,
                        currency: props.currency.code,

                    }).then((paypal) => {

                        paypal
                            .Buttons({
                                createOrder: function (data, actions) {
                                    // Set up the transaction details
                                    return actions.order.create({
                                        purchase_units: [
                                            {
                                                reference_id: activePlan.value.plan_id,
                                                description: translate('Plan subscription'),
                                                custom_id: activePrice.value,
                                                amount: {
                                                    currency_code: props.currency.code,
                                                    value: cost(), // Set your payment amount here
                                                },
                                            },
                                        ],
                                    });
                                },
                                onApprove: function (data, actions) {
                                    // Capture the payment when the user approves
                                    return actions.order.capture().then(function (details) {
                                        // Handle the successful payment
                                        validateOrderPayment(details, details.id);
                                    });
                                },
                            })
                            .render("#paypal-button-container")
                            .catch((error) => {
                                console.error("failed to render the PayPal Buttons", error);
                            });
                        showLoader.value = false;

                    }).catch((error) => {
                        console.error("failed to load the PayPal JS SDK script", error);
                    });
                    
                });
            }

            
            /**
             * Check if currency already updated localy today 
             * using CurrencyAPI.
             * 
             * @param {String} currency 
             */
            const checkLocalUpdatedCurrency = async (currency) => 
            {
                return await handleGetRequest(props.conf.url+'admin/load_currencies').then(response => {
                    if (response == null)
                    {
                        return;
                    }
                    currencyConverted.value = 0;
                    for (let i = 0; i < response.length; i++) {
                        const element = response[i];
                        if (element.code == currency)
                        {
                            currencyConverted.value = element.ratio;
                        }
                    }
                    console.log(currencyConverted.value);

                });
            }
            
            
            /**
             * Update currency ration & last check date 
             * using CurrencyAPI.
             * 
             * @param {String} currency 
             */
            const updateCurrencyRatio = async (currency, ratio) => 
            {
                ratio
                const params = new URLSearchParams([]);
                params.append('type', 'Currency.update');
                params.append('params[code]', currency);
                params.append('params[ratio]', Math.round(ratio));
                handleRequest(params, '/api/update').then(async (data)  => {
                    console.log(data);
                });
            }
            
            /**
             * Convert currency using CurrencyAPI.
             * @param {String} base 
             * @param {String} to 
             */
            const convertRates = async (base, to) => 
            {
                await checkLocalUpdatedCurrency(to);

                if (currencyConverted.value > 0)
                {
                    return currencyConverted.value;

                } else {
                    
                    var result = await currencyApi.latest({
                        base_currency: base,
                        currencies: to,
                    }).then(result => {
                        currencyConverted.value = result.data[to].value;
                        updateCurrencyRatio( to, result.data[to].value)
                        return currencyConverted.value;
                    });

                    return currencyConverted.value;
                }

            }


            const completePaystack = async () => {

                showLoader.value = true;
                
                await convertRates(props.currency.code, 'NGN');
                
                const params = new URLSearchParams([]);
                params.append('type', 'User.get_started_save_plan');
                params.append('params[plan_id]', activePlan.value.plan_id);
                params.append('params[payment_type]', activePrice.value);
                params.append('params[payment_method]', paymentMethod.value);
                handleRequest(params, '/api/create').then(async (data)  => {
                    const amount = Math.round((currencyConverted.value * cost()) * 100); // Amount in kobo (10000 kobo = 100 NGN)
                    try {
                        const response = await axios.post('https://api.paystack.co/transaction/initialize', {
                            email: props.auth.email,  // Customer's email
                            amount: amount,  
                        }, {
                        headers: {
                            'Authorization': 'Bearer '+props.setting.paystack_public_key,
                            'Content-Type': 'application/json'
                        }
                        });
                        
                        // Redirect to Paystack payment page
                        window.open(response.data.data.authorization_url);
                        var reference = response.data.data.reference
                        var  interval = setInterval(() => {
                            verifyPaystackPayment(reference);
                        }, 5000);

                    } catch (error) {
                        console.error('Error initiating payment:', error);
                    }
                });
            }

            const verifyPaystackPayment = async (reference)  =>  
            {
                const response = await axios.get("https://api.paystack.co/transaction/verify/"+reference,{
                        headers: {
                            'Authorization': 'Bearer '+props.setting.paystack_public_key,
                            'Content-Type': 'application/json'
                        }});
                
                if (response.data.data.status == 'success')
                {
                    return validateOrderPayment(response.data.data, response.data.data.id);
                }
            }
            /**
             * Validate paypal payment
             */
            const validateOrderPayment = (order, paymentCode) => {
                showLoader.value = true;
                const params = new URLSearchParams([]);
                params.append('type', 'Payment.paypal_payment_confirmation');
                params.append('params[plan_id]', activePlan.value.plan_id);
                params.append('params[payment_type]', activePrice.value);
                params.append('params[payment_method]', paymentMethod.value);
                params.append('params[status]', 'paid');
                params.append('params[cost]', cost());
                params.append('params[payment_code]', paymentCode);
                params.append('params[order]', JSON.stringify(order));
                handleRequest(params, '/api/create').then(data => {
                    showLoader.value = false;
                    if (data.success) {
                        showAlert(data.result);
                        window.location.href = props.conf.url+'admin/profile'
                    }
                });
            }

            /**
             * Set active plan
             */
            const setActivePlan = (plan) => {
                activePlan.value = plan
            }

            return {
                paymentMethod,
                activePlan,
                activePrice,
                showEditSide,
                steps,
                pricesList,
                paypalScriptLoaded,
                completePaystack,
                setActivePlan,
                closeSide,
                today,
                endDate,
                switchPlanPrice,
                cost,
                handleActiveStep,
                saveBusiness,
                validateStep,
                setActiveStep,
                planCost,
                setBusinessType,
                complete,
                showLoader,
                url,
                content,
                activeItem,
                activeStep,
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
            'currency'
        ],
    };
</script>
<style lang="css">.rtl #side-cart-container {
    right: auto;
    left: 0;
}</style>