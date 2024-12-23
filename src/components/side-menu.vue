<template>

    <!--begin::Sidebar-->
    <div id="kt_app_sidebar" class="app-sidebar flex-column " data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
        data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
        data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle" :class="menuclass" >


        <!--begin::Logo-->
        <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
            <!--begin::Logo image-->
            <a class="pt-10" v-if="system_setting" href="/dashboard">
                <img alt="Logo" :src="system_setting.backend_logo" class="app-sidebar-logo-default w-2/3" />
                <img alt="Logo" :src="system_setting.backend_logo" class="app-sidebar-logo-minimize w-2/3" />
            </a>
            <!--end::Logo image-->

            <div id="kt_app_sidebar_toggle"
                class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate "
                data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                data-kt-toggle-name="app-sidebar-minimize">

                <i class="ki-duotone ki-black-left-line fs-3 rotate-180"><span class="path1"></span><span
                        class="path2"></span></i>
            </div>


            <!--end::Sidebar toggle-->
        </div>
        <!--end::Logo-->
        <!--begin::Footer-->
        <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 relative overflow-auto"  :style="isDesktop == true ? 'height:calc(100vh - 120px)' : 'height:calc(100vh - 50px)'" id="kt_app_sidebar_footer">
            

            <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                <!--begin::Menu wrapper-->
                <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
                    <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" >
                        <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" v-if="checkAccess">
                            <div v-for="(menu, i) in pages" data-kt-menu-trigger="click" class="menu-item  menu-accordion">

                                <a :key="showMenu"  class="menu-link gap-2" v-on:click.prevent="openPage(menu)" :class="menu.class"
                                    :href="url+menu.link">
                                    <span class="menu-bullet">
                                        <vue-feather :type="menu.icon"></vue-feather>
                                    </span> 
                                    <span class="menu-title" v-text="menu.title"></span>
                                    <vue-feather v-if="menu.sub && !menu.show_sub" type="chevron-down"></vue-feather>
                                </a>
                                <!--end:Menu link--><!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion show" :key="showMenu"  v-if="menu.sub && menu.show_sub ">
                                    <div class="menu-item" v-for="submenu in menu.sub">
                                        <a v-on:click.prevent="openPage(submenu)" :class="submenu.class"
                                            :href="url+submenu.link" class="menu-link gap-1">
                                            <vue-feather type="chevron-right"></vue-feather>
                                            <span class="menu-title" v-text="submenu.title"></span>
                                        </a>
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

    import { ref } from 'vue';
    import { translate,checkAccess } from '@/utils.vue';

    export default {
        components: { },
        emits: ['callback'],
        setup(props, {emit}) {
            
            const showMenu = ref(true);
            const same_page = ref(false);
            const pages = ref();
            pages.value = props.menus;
            const activePage = ref();
            activePage.value = props.samepage;

            const resetClasses = () => 
            {
                let pagesArr = pages.value;

                for (var a = pagesArr.length - 1; a >= 0; a--) {
                    pagesArr[a].class = pagesArr[a].link == activePage.value ? 'text-white active' : null;
                    pagesArr[a].show_sub = pagesArr[a].link == activePage.value ? true : false;
                    if (pagesArr[a].sub && pagesArr[a].sub.length) {

                        for (var i = pagesArr[a].sub.length - 1; i >= 0; i--) {
                            pagesArr[a].sub[i].class = pagesArr[a].sub[i].link == activePage.value ? 'active ' : null;
                            pagesArr[a].class = pagesArr[a].sub[i].link == activePage.value ? 'text-white active' : pagesArr[a].class;
                            pagesArr[a].show_sub = pagesArr[a].sub[i].link == activePage.value ? true : pagesArr[a].show_sub;

                        }
                    }
                }
                showMenu.value = true
            }
            
            resetClasses();

            const openPage = (page) => {

                activePage.value = page.link;
                page.sub ? null : emit('callback', page);
                resetClasses()
            }

            const isDesktop = () => {
                return window.screen.availWidth > 1000 ? true : false;
            }
            
            isDesktop();

            return {
                resetClasses,
                openPage,
                showMenu,
                same_page,
                pages,
                activePage,
                isDesktop,
                translate,
                checkAccess,
            }
        },
        props: ['url', 'menus', 'menuclass', 'samepage', 'auth', 'system_setting'],
        created: function () {
        },
        methods: {
            
        }
    };
</script>

<style lang="css">
    .sidebar-menu {
        min-height: calc(100vh - 100px);
    }
</style>