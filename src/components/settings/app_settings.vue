<template>
    <div class="w-full " v-if="content">

        <div class="card mb-9">
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                    <!--begin::Image-->
                    <div v-if="content.app_setting" class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                        <img class="mw-50px mw-lg-75px" :src="content.app_setting.logo" alt="image">
                    </div>
                    <!--end::Image-->

                    <!--begin::Wrapper-->
                    <div class="flex-grow-1">
                        <!--begin::Head-->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <!--begin::Details-->
                            <div class="d-flex flex-column">
                                <!--begin::Status-->
                                <div class="d-flex align-items-center mb-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3" v-text="content.title"></a>
                                    <span class="badge badge-light-success me-auto"></span>
                                </div>
                                <!--end::Status-->

                                <!--begin::Description-->
                                <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500">

                                </div>
                                <!--end::Description-->
                            </div>
                            <!--end::Details-->
                        </div>
                        <!--end::Head-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap justify-content-start">
                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap">
                                <!--begin::Stat-->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bold">3</div>
                                    </div>
                                    <!--end::Number-->

                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-500">Drivers</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->

                                <!--begin::Stat-->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bold counted" >75</div>
                                    </div>
                                    <!--end::Number-->

                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-500">Customers</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->

                                <!--begin::Stat-->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-arrow-up fs-3 text-success me-2"><span class="path1"></span><span class="path2"></span></i> <div class="fs-4 fw-bold counted" >134</div>
                                    </div>
                                    <!--end::Number-->                                

                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-500">Successful Trips</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Stats-->

                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Details-->

                <div class="separator"></div>

                <!--begin::Nav-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    
                        <!--begin::Nav item-->
                        <li v-for="(row,k) in content.fillable" class="nav-item">
                            <a @click="switchTab(k)" v-text="handleTabName(k)" class="nav-link text-active-primary py-5 me-6 " :class="activeTab == k ? 'active' : ''"  href="javascript:;"></a>
                        </li>
                        <!--end::Nav item-->
                </ul>
                <!--end::Nav-->
            </div>
        </div>



        <div class=" w-full">
            <form action="/api/update" method="POST" data-refresh="1" id="system-setting-form" class="action  px-4 m-auto rounded-lg pb-10" >

                <input name="type" type="hidden" value="AppSettings.update">
                <input name="app" type="hidden" v-model="content.app_type">

                <div class="w-full "  v-for="(row,k) in content.fillable">
                    <div class="card w-full py-10" v-if="activeTab == k">
                        <div class="card-body pt-0"  >
                            <div class="settings-form" >
                                <div class="row mb-6"   v-for="(field, i) in row" >
                
                                    <label class="col-lg-4 col-form-label " :class="field.required ? 'required' : ''" :for="'input'+i"  v-if="field.column_type != 'hidden'">
                                        <p class="fw-bold fs-4 w-full" v-text="field.title" ></p>
                                        <p v-text="field.help_text" v-if="field.help_text" ></p>
                                    </label>
                                    <form_field  class="col-lg-8 fv-row fv-plugins-icon-container"   :column="field"   :item="content.app_setting" :conf="conf"></form_field>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <button class="uppercase mt-3 text-white mx-auto rounded-lg bg-purple-800 hover:bg-red-800 px-4 py-2">{{translate('Save')}}</button>
            </form>
        </div>
                   
    </div>
</template>
<script>

import {ref } from 'vue';
import {translate,handleGetRequest,isInput,setActiveStatus, handleName, handleTabName } from '@/utils.vue';
import field from '@/components/includes/Field.vue';
import form_field from '@/components/includes/form_field.vue';

export default  
{
    components: {
        'vue-medialibrary-field': field,
        form_field,
        translate
    },
    name:'App Settings',
    setup(props) {
        
        const url =  props.conf.url+props.path+'?load=json';
                
        const content =  ref({
            title: '',
            app_setting: [],
            
        });
        
        const item =  props.setting;

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
                item.value = content.value.app_setting;
            });
        }
        
        load();

        const activeTab =  ref('basic');

        const switchTab = (tab) => 
        {
            activeTab.value = tab;
        }

        

        return {
            url,
            content,
            item,
            isInput,
            setActiveStatus,
            handleTabName,
            handleName,
            switchTab,
            activeTab,
            translate,
        };
    },
    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
    ]
};
</script>
<style lang="css">
    .rtl #side-cart-container
    {
        right: auto;
        left:0;
    }
</style>