<template>
    <div>

        <div v-if="loader" class="bg-white fixed w-full h-full top-0 left-0" style="z-index:99999; opacity: .9;">
            <img class="m-auto w-500px" :src="'/uploads/loader.gif'" />
        </div>
        <div class="left-4">
            <!-- component -->
            <div class="w-full relative">
                <navbar @togglemenu="toggleMenuClass" :langs="langs" v-if="auth" style="z-index: 9999;" :system_setting="system_setting" :conf="conf" :auth="auth" ></navbar>
                <div class="gap gap-6 h-full flex w-full overflow-hidden   ">
                    <side-menu @callback="switchTab" :samepage="activeTab" :system_setting="system_setting" :auth="auth" :url="conf.url ? conf.url : '/'" :key="menuClass" :menus="main_menu" class="sidebar " id="sidebar" v-if="showSide" :menuclass="menuClass" style="z-index:9999"></side-menu>
                    
                    <div @click="checkMobileMenu()" v-if="auth" class="w-full flex overflow-auto" >
                        <div class="w-full" v-if="checkAccess()">
                            <transition  :duration="1000">
                                <component @callback="switchTab"  :langs="langs" class="pt-8 px-1 min-h-400px" ref="activeTab" :types-list="typesList"  :key="activeTab" :path="activeTab" :system_setting="system_setting" :setting="setting" :conf="conf" :auth="auth" :is="activeComponent" :currency="currency"></component>
                            </transition>
                        </div>

                    </div>
                    <div v-else class="w-full flex overflow-auto" >
                        <login form_action="/" ></login>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</template>
<script>

import {defineAsyncComponent} from 'vue';
import SideMenu from '@/components/side-menu.vue'; 
import navbar from '@/components/navbar.vue'; 
import dashboard from '@/components/dashboard.vue'; 
import master_dashboard from '@/components/master_dashboard.vue'; 
import HelpMessages from '@/components/help_messages.vue'; 
import {translate, handleAccess, handleRequest, handleGetRequest, showAlert} from '@/utils.vue';

// Default data table pages
const data_table = defineAsyncComponent(() => import('@/components/datatable_pages/data_table_page.vue') );

const roles = defineAsyncComponent(() => import('@/components/roles.vue') );

const system_settings = defineAsyncComponent(() => import('@/components/settings/system_settings.vue') );

const users = defineAsyncComponent(() => import('@/components/users.vue') );

const blog = defineAsyncComponent(() => import('@/components/blog/index.vue') );

const app_settings = defineAsyncComponent(() => import('@/components/settings/app_settings.vue') );

const profile = defineAsyncComponent(() => import('@/components/profile/profile.vue') );

const gallery = defineAsyncComponent(() => import('@/components/gallery/index.vue') );

const translations = defineAsyncComponent(() => import('@/components/translations.vue') );

const contact_forms = defineAsyncComponent(() => import('@/components/contact_forms.vue') );

const menus = defineAsyncComponent(() => import('@/components/menus.vue') );

const media = defineAsyncComponent(() => import('@/components/media/index.vue') );

const hooks = defineAsyncComponent(() => import('@/components/hooks/index.vue') );

const plugins = defineAsyncComponent(() => import('@/components/plugins/index.vue') );

const categories = defineAsyncComponent(() => import('@/components/categories/index.vue') );

const pages = defineAsyncComponent(() => import('@/components/pages/index.vue') );

const notifications_events = defineAsyncComponent(() => import('@/components/notifications_events.vue') );

const packages = defineAsyncComponent(() => import('@/components/packages.vue') );

const invoices = defineAsyncComponent(() => import('@/components/invoices.vue') );

const transactions = defineAsyncComponent(() => import('@/components/transactions.vue') );

const artists = defineAsyncComponent(() => import('@/components/artist/index.vue') );





export default {
    name: 'app',
    components: {
        artists,
        transactions,
        invoices,
        packages,
        categories,
        data_table,
        SideMenu,
        navbar,
        dashboard,
        master_dashboard,
        roles,
        system_settings,
        users,
        pages,
        app_settings,
        profile,
        gallery,
        translations,
        contact_forms,
        menus,
        notifications_events,
        plugins,
        hooks,
        media,
        blog,
        pages,
        translate,
        'help_messages':HelpMessages,
      },
      data() {
        return {
            langs: [],
            date: '',
            loader: true,
            activeItem: null,
            showAddSide: false,
            showEditSide: false,
            showTab: true,
            activeComponent: 'dashboard',
            activeTab: 'dashboard',
            status: null,
            lang: {},
            auth: {},
            currency: {},
            setting: {},
            system_setting: {},
            conf: {},
            url: '/',
            main_menu: [],
            typesList: [],
            showSide: true,
            showModal: false,
            activeModal: null,
            menuClass: ''
        };
    },
    mounted() {
        
        const t = this;

        this.setProps();

        // $(window).on('popstate', function(e) {
        //     t.switchTab({link:window.location.pathname.replace('/','')})
        // });
        
        $(document).on('submit', 'form',function (e) {
            e.preventDefault();
            t.submit(this, e)
        })

        this.showSide =  (window.screen.availWidth > 1000 ) ? true : false;

        // Check if Native notifications enabled  from Master
        if (this.system_setting && this.system_setting.enable_notifications)
            // this.notify()

        this.checkMobileMenu()    

    },
    methods: 
    {
        /**
         * Check if the user has access 
         * to specific permission
         */
        can(i)
        {
            if (!this.auth )
                return null;

            if (i && this.auth.permissions)
                return this.auth.permissions[i]
                
            return null;
        },
        checkAccess()
        {
          if (this.auth && this.auth.role_id == 1){
            return true;
          }

          if (!this.auth)
            return false;

          return true;
        },

        /**
         * Close menu at mobile and 
         * small screen devices
         */
        checkMobileMenu()
        {
            if (window.innerWidth < 800)
            {
                this.menuClass = '';
            }
        },

          
        /**
         * Check notifications permission
         * Send welcome notification
         */ 
        notify (title, body) 
        {

            if (this.system_setting.notifications_welcome_message)
                return null;

        },


        /**
         * Get the props for App root
         */
        setProps()
        {

            const mountEl = document.getElementById("root-parent");
            let propsSet = { ...mountEl.dataset };
            handleGetRequest('/api/load_config?component='+propsSet.component).then(response => {
                this.setPropsData(response, response.app, propsSet.page)
                this.loader = false;
            })
        },

        /**
         * Get the props for App root
         */
        setPropsData(response, app, page)
        {
            if (app && response)
            {
                this.url = app.conf ? app.conf.url : '/';
                this.activeTab = page ?? this.defaultPage();
                this.auth = app.auth ?? {};
                this.main_menu = response.menu ?? {};
                this.setting = app.setting ?? {};
                this.system_setting = app.setting ?? {};
                this.conf = app['CONF'] ?? {};
                this.activeComponent = response.component ?? this.defaultPage();
                this.currency = app.currency ?? {};
                this.lang = response.lang_json ?? {};
                this.langs = response.langs ?? {};
                this.menuClass = ' '
            }
            
        },


        /**
         * Switch between Tabs
         */ 
        switchTab(tab) {
            if (!tab.sub)
            {
                this.activeTab = (tab && tab.link) ? tab.link : this.defaultPage();
                this.activeComponent = (tab && tab.component) ? tab.component : this.activeTab;
                history.pushState({menu: JSON.parse(JSON.stringify(tab))}, '', this.conf.url+this.activeTab);
                this.checkMobileMenu()
            }
        },
        
        /**
         * The default page to load if 
         * loaded component is not found
         */
        defaultPage()
        {
            return 'dashboard';
        }, 

        /**
         * If the route is invoice 
         * load its custom component
         */  
        checkIsInvoice()
        {
            return this.activeTab.includes('invoices/show') ? true : null;

        },
        setValues(data) {
            this.content = JSON.parse(JSON.stringify(data)); return this
        },

        toggleMenuClass() {
            this.showSide = true ; 
            this.menuClass = 'drawer drawer-start drawer-on' 
        },

        submit(element, props)
        {
            try {
                this.loader = true;
                let Things = $(element).serializeArray()
                var params = new URLSearchParams();
                Things.map(function(n){
                    params.append([n['name']],  n['value']);
                });
                
                var t = this;

                handleRequest(params, $(element).attr('action')).then(response => {
                    t.loader = false;
                    handleAccess(response)

                }).catch(error => {
                    showAlert(error)
                })
            } catch (error) {
                console.log(error)
            }
        },

    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

<style>
@import './assets/bootstrap-grid.min.css';
@import './front_assets/css/tailwind.min.css';
@import './assets/media-library.css';
@import './assets/style.bundle.css';


/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: all 0.8s ease-in-out;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
  position: absolute ;
  /* top:0; */
  transition: all 0.1s ease-in-out;
}

@media (min-width: 1024px)
{
    .flex.flex-wrap.lg\:flex-nowrap
    {
        flex-wrap: nowrap !important;
    }
}
</style>