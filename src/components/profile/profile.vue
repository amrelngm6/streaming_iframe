<template>
    <div class="w-full mb-5 mb-xl-10">
       
            <div class="card p-6">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <img :src="auth.photo" class="w-20" alt="image" />
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"
                                        v-text="auth.name"></a>
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <vue-feather class="w-5 mx-1" type="eye"></vue-feather>
                                        <span v-text="auth.role.name"></span>
                                    </a>
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <vue-feather class="w-5 mx-1" type="smartphone"></vue-feather>
                                        <span v-text="auth.phone"></span>
                                    </a>
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <vue-feather class="w-5 mx-1" type="at-sign"></vue-feather>
                                        <span v-text="auth.email"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex my-4">
                                <a @click="showEditSide = true" href="javascript:;" class="text-white btn btn-sm btn-primary me-3" v-text="translate('Edit')"></a>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                
                                <!--end::Stats-->
                            </div>
                            <!--end::Wrapper-->

                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Info-->
                </div>
            </div>
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <li class="nav-item mt-2" v-for="tab in tabsList">
                    <a v-if="checkRole(activeItem.role_id, tab.link)" @click="setActiveTab(tab.link)" :class="activeTab == tab.link ? 'active' : ''"
                        class="nav-link text-active-primary ms-0 me-10 py-5 " href="javascript:;" v-text="tab.title"></a>
                </li>
            </ul>

        <div class="w-full pt-9 pb-0">
            <div class="d-flex flex-column flex-lg-row">
                <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                    
                    <account_tab :currency="currency"  v-if="activeTab == 'account'" :overview="content.overview" :item="activeItem" />
                   
                    <info_tab :currency="currency"  :conf="conf" v-if="activeTab == 'business_info'" :overview="content.overview" :item="activeItem"  />

                    <setting_tab :conf="conf" v-if="activeTab == 'settings'" :fillable="content.fillable" :item="activeItem"  :system_setting="system_setting" />

                </div>  

                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2" v-if="activeItem">
                    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="summary" data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto" data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <div class="card-header">
                            <div class="card-title"><h2 v-text="translate('Summary')"></h2></div>
                        </div>
                        <div class="card-body pt-0 fs-6">
                            <div class="mb-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-60px symbol-circle me-3">
                                        <img alt="Pic" :src="activeItem.picture ?? '/uploads/images/default_profile.png'">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-2" v-text="activeItem.name"></a>
                                        <a href="#" class="fw-semibold text-gray-600 text-hover-primary" v-text="activeItem.email"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="separator separator-dashed mb-7"></div>

                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        <side-form-update @callback="closeSide" :conf="conf" model="User.update" :item="activeItem"
            :model_id="activeItem.id" index="id" v-if="showEditSide" :columns="content.fillable" class="col-md-3" />

    </div>
</template>
<script>

import { defineAsyncComponent, ref } from 'vue';
import { translate, handleGetRequest, handleName, isInput, setActiveStatus, handleRequest, handleAccess, deleteByKey, showAlert } from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-update.vue')
);

const form_field = defineAsyncComponent(() => import('@/components/includes/form_field.vue') );
const account_tab = defineAsyncComponent(() => import('@/components/profile/account.vue') );
const setting_tab = defineAsyncComponent(() => import('@/components/profile/setting.vue') );
const info_tab = defineAsyncComponent(() => import('@/components/profile/info.vue') );

export default {

    components: {
        SideFormUpdate,
        SideFormCreate,
        account_tab,
        form_field,
        setting_tab,
        info_tab

    },
    name: 'Users',
    setup(props) {

        const url = props.conf.url + props.path + '?load=json';

        const showAddSide = ref(false);
        const showEditSide = ref(false);
        const activeItem = ref({});
        const content = ref({});
        const activeTab = ref('account');
        const tabsList = ref([
            { title: translate('Account'), link: 'account' },
            { title: translate('Settings'), link: 'settings' },
        ]);



        const setActiveTab = (tab) => {
            activeTab.value = tab;
        }

        const load = () => {
            handleGetRequest(url).then(response => {
                content.value = JSON.parse(JSON.stringify(response));
                activeItem.value = content.value.user;
            });
        }
        
        load();
        
        const closeSide = () => {
            showAddSide.value = false;
            showEditSide.value = false;
        }

        const sameRole = (user, role) => {
            if (user.role_id == role.role_id) {
                return true
            }
            return false;
        }

        const checkRole = (role_id, tab) => {
            
        }


        const cost = () => 
        {
            return 0;
        }

        const checkFeatureLimit = (features, code) => 
        {
            if ( features)
            {
                for (let i = 0; i < features.length; i++) {
                    const element = features[i];
                    if (element.code == code+'.count')
                    {
                        return element.access_value;
                    }
                }
            }
            return 0;
        }


        

        
        

        
        return {
            checkFeatureLimit,
            cost,
            url,
            showAddSide,
            showEditSide,
            content,
            setActiveTab,
            tabsList,
            activeTab,
            activeItem,
            checkRole,
            closeSide,
            sameRole,
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
        'system_setting',
        
        'conf',
        'auth',
        'currency'
    ]
};
</script>