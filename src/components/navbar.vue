<template>
    <div id="header" class="relative w-full" >

            <!--begin::Header-->
            <div id="kt_app_header" class="app-header bg-white shadow" data-kt-sticky="true"
                data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
                data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false" style="max-height:50px">

                <!--begin::Header container-->
                <div class="app-container  container-fluid d-flex align-items-stretch justify-content-between "
                    id="kt_app_header_container">

                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" @click="showMenu"
                            id="kt_app_sidebar_mobile_toggle">
                            Menu
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="/dashboard" class="d-lg-none">
                            <img alt="Logo" :src="system_setting.backend_logo" class="h-30px" />
                        </a>
                    </div>
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
                        <div class=" app-header-menu  app-header-mobile-drawer  align-items-stretch ">
                            <div class="menu  menu-rounded  menu-column  menu-lg-row my-5  my-lg-0  align-items-stretch fw-semibold px-2 px-lg-0 " id="kt_app_header_menu" data-kt-menu="true">
                                <div class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                                    <a href="/" target="_blank" class="menu-link">
                                        <span class="menu-title" v-text="system_setting.sitename"></span>
                                        <span class="menu-arrow d-lg-none"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="app-navbar flex-shrink-0" >
                            <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle" @mouseover="showSubMenu = true"  >
                                <div  class="cursor-pointer symbol symbol-35px flex gap-4">
                                    <span class="pt-4" v-text="auth.name"></span>
                                    <img v-if="auth" :src="auth.photo" class="rounded-3" alt="user">
                                </div>

                                <div v-if="showSubMenu" @mouseleave="showSubMenu = false" class="shadow bg-white menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px show top-20" data-kt-menu="true" :class="translate('is_rtl') == 'rtl' ? 'left-4' : 'right-4'" data-popper-placement="bottom-end" style="z-index: 107; position: fixed;">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <img :alt="auth.name" :src="auth.photo">
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5"><span v-text="auth.name"></span></div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7" v-text="auth.email"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-2"></div>

                                    <div class="menu-item px-5" v-if="auth.business">
                                        <span class="menu-link px-5" v-text="auth.business.business_name"></span>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="/admin/profile" class="menu-link px-5" v-text="translate('Profile')"></a>
                                    </div>
                                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                        <a href="#" @click="showLangs = !showLangs" class="menu-link px-5">
                                            <span class="menu-title position-relative">
                                                <span v-text="translate('Language')"></span>
                                                <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 flex"  :class="translate('is_rtl') == 'rtl' ? 'left-0' : 'right-0'">
                                                    <span v-text="translate('Language')"></span> <img class="w-15px h-15px rounded-1 ms-2" :src="translate('is_rtl') == 'rtl' ? '/uploads/img/flags/egypt.svg' : '/uploads/img/flags/united-states.svg'" alt="">
                                                </span>
                                            </span>
                                        </a>

                                        <div class="menu-sub menu-sub-dropdown w-175px py-4 bg-white absolute show active" v-if="showLangs" style="display:block;">
                                            <div v-for="language in langs " class="menu-item px-3">
                                                <a v-if="language" :href="'/switch-lang/'+language.language_code" class="menu-link d-flex px-5 active">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" :src="language.icon" alt="">
                                                    </span> 
                                                    <span v-text="language.name"></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-item px-5 my-1">
                                        <a :href="auth.role_id > 1 ? '/admin/settings' : '/admin/system_settings'" class="menu-link px-5" v-text="translate('Settings')"></a>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="/logout" class="menu-link px-5" v-text="translate('Logout')"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Header-->
            
    </div>
</template>

<script>
import notifications_popup from './notifications_popup.vue'
import {ref} from 'vue'
import { translate } from '@/utils.vue';

export default {
  name: 'navbar',
  components: {
    notifications_popup,
  },
  emits: ['togglemenu'],
  setup(props, {emit}) {

    const showSubMenu = ref(); 
    const showLangs = ref(false); 
    
    const showMenu = () => {
        emit('togglemenu')
    }; 

    return {
        showSubMenu,
        showLangs,
        showMenu,
        translate
    };

  },
  props: {
    auth:[Object, null],
    lang:[Object, null],
    langs:[Array, null],
    system_setting:[Object, null],
    conf:[Object, null]
  }
}
</script>
<style lang="css">
.has-arrow.main-drop
{
    width: max-content
}
</style>