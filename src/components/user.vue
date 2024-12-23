<template>
    <div class="d-flex flex-column flex-xl-row" :key="activeItem">
        <!--begin::Sidebar-->
        <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">

            <!--begin::Card-->
            <div class="card mb-5 mb-xl-8">
                <!--begin::Card body-->
                <div class="card-body pt-15" v-if="activeItem.business ">
                    <!--begin::Summary-->
                    <div class="d-flex flex-center flex-column mb-5">
                        <!--begin::Avatar-->
                        <div class="symbol symbol-150px symbol-circle mb-7">
                            <img :src="activeItem.photo" alt="image">
                        </div>
                        <!--end::Avatar-->

                        <!--begin::Name-->
                        <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1" v-text="activeItem.name"></a>
                        <!--end::Name-->

                        <!--begin::Email-->
                        <a href="#" class="fs-5 fw-semibold text-muted text-hover-primary mb-6" v-text="activeItem.email">
                        </a>
                        <!--end::Email-->
                    </div>
                    <!--end::Summary-->

                    <div class="separator separator-dashed my-3"></div>

                    <!--begin::Details content-->
                    <div class="pb-5 fs-6 hide hidden ">
                        <!--begin::Details item-->
                        <div class="fw-bold mt-5" v-text="translate('ID')"></div>
                        <div class="text-gray-600" v-text="activeItem.id"></div>
                        <div class="fw-bold mt-5" v-text="translate('email')"></div>
                        <div class="text-gray-600" v-text="activeItem.email"></div>
                        <div class="fw-bold mt-5" v-text="translate('mobile')"></div>
                        <div class="text-gray-600" v-text="activeItem.mobile"></div>
                        <div class="fw-bold mt-5" v-text="translate('Address')"></div>
                        <div class="text-gray-600" v-text="activeItem.address"></div>
                        <div class="fw-bold mt-5" v-text="translate('language')"></div>
                        <div class="text-gray-600" v-text="activeItem.language"></div>
                        <!--begin::Details item-->
                    </div>
                    <!--end::Details content-->
                </div>
                <!--end::Card body-->
            </div>
        </div>
        <!--end::Sidebar-->

        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-15 relative">
            <close_icon class="p-1 absolute cursor-pointer right-0" @click="closeSide"></close_icon>
            <!--begin:::Tabs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8"
                role="tablist">
                <!--begin:::Tab item-->
                <li class="nav-item" role="presentation" v-for="tab in tabsList">
                    <a @click="setActiveTab(tab.link)" :class="activeTab == tab.link ? 'active' : ''" v-text="tab.title"
                        class="nav-link text-active-primary pb-4 " data-bs-toggle="tab" href="javascript:;"></a>
                </li>
                <!--end:::Tab item-->
            </ul>
            <!--end:::Tabs-->

            <!--begin:::Tab content-->
            <div class="tab-content" id="myTabContent">

                <div class="d-flex flex-column flex-lg-row" id="kt_profile_details_view" v-if="activeTab == 'account'">
                    <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <div class="row py-4 my-2 border-b border-gray-200" >
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('id')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800" v-text="activeItem.id"></span></div>
                            </div>
                            <div class="row py-4 my-2 border-b border-gray-200" >
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Name')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800" v-text="activeItem.name"></span></div>
                            </div>
                            <div class="row py-4 my-2 border-b border-gray-200" >
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('email')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800" v-text="activeItem.email"></span></div>
                            </div>
                            <div class="row py-4 my-2 border-b border-gray-200" >
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('mobile')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800" v-text="activeItem.mobile"></span></div>
                            </div>
                            <div class="row py-4 my-2 border-b border-gray-200" >
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('status')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800" v-text="activeItem.active ? 'Yes' : 'No'"></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-5 mb-xl-10" id="kt_profile_details_view" v-if="activeTab == 'business_info'">
                    <div class="card-body p-9"  v-if="activeItem.business" >
                        
                        <div class="row py-4 my-2 border-b border-gray-200" >
                            <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Business name')"></label>
                            <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800"v-text="activeItem.business.business_name"></span></div>
                        </div>
                        <div class="row py-4 my-2 border-b border-gray-200" >
                            <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Business type')"></label>
                            <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800"v-text="activeItem.business.type"></span></div>
                        </div>
                    </div>
                </div>

                <div class="w-full" v-if="activeTab == 'business_info'">
                    
                </div>
            </div>
        </div>
    </div>

</template>
<script>

import { defineAsyncComponent, ref } from 'vue';
import { translate, handleGetRequest, handleName, isInput, sameRole, setActiveStatus, handleRequest, deleteByKey, showAlert } from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-update.vue')
);


const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);

const close_icon = defineAsyncComponent(() =>
    import('@/components/svgs/Close.vue')
);

export default {

    components: {
        SideFormCreate,
        SideFormUpdate,
        form_field,
        close_icon,

    },
    name: 'Users',
    emits: ['callback'],
    setup(props, { emit }) {

        const url = props.conf.url + props.path + '?load=json';

        const showAddSide = ref(false);
        const showEditSide = ref(false);
        const content = ref({});

        const activeTab = ref('account');
        const tabsList = ref([
            { title: translate('Account info'), link: 'account' },
            { title: translate('Business info'), link: 'business_info' },
        ]);

        const activeItem = props.item;
        const calcDaysWidth = () => {
        }

        const setActiveTab = (tab) => {
            activeTab.value = tab;
        }

        const closeSide = () => {
            emit('callback')
        }

        
        const  stats = ref({});
        const  loadStats = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
            });
        }

        loadStats();

        return {
            url,
            stats,
            sameRole,
            showAddSide,
            showEditSide,
            content,
            setActiveTab,
            tabsList,
            activeTab,
            activeItem,
            closeSide,
            calcDaysWidth,
            setActiveStatus,
            isInput,
            handleName,
            translate,
        };
    },

    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
        'fields',
        'stats',
        'item',
        'currency'
    ]
};
</script>