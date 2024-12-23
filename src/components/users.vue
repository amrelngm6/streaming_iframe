<template>
    <main v-if="content" class=" flex-1 overflow-x-hidden overflow-y-auto  w-full">
        <user :currency="currency" :key="activeItem" @callback="closeSide" :fields="content.overview" :item="activeItem" :path="path" :conf="conf" :auth="auth" :setting="setting" v-if="showUser"></user>
        <!-- New releases -->
        <div class="px-4 mb-6 py-4 rounded-lg shadow-md bg-white dark:bg-gray-700 flex w-full">
            <h1 class="font-bold text-lg w-full" v-text="content.title"></h1>
            <a href="javascript:;" class="menu-dark uppercase p-2 mx-2 text-center text-white w-32 rounded bg-danger" @click="showAddSide = true, activeItem = {}; " v-text="translate('add_new')"></a>
        </div>
        <div class="w-full " v-if="content && content.roles" >

            <div class="w-full" :key="role.users" v-for="role in content.roles">
                
                <h3  class="pb-b flex gap-4"><span v-text="role.name"></span> <span class="pt-2 text-sm text-muted" v-text="role.id > 1 ? translate('Theese users can manage your account only') : ''"></span></h3>

                <div class="row g-6 mb-6 g-xl-9 mb-xl-9" :key="role.users" >

                    <div v-for="user in role.users" class="col-md-3  col-sm-6 col-xxl-4">
                        <div class="card ">
                            <div class="card-body d-flex flex-center flex-column py-9 px-5">
                                <div class="symbol symbol-65px symbol-circle mb-5">
                                    <img :src="user.photo" alt="image">
                                </div>
                                <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{user.first_name}} {{user.last_name}}</a>
                                <div class="fw-semibold text-gray-500 mb-2" v-text="user.email"></div>
                                <div class="fw-semibold text-gray-500 mb-6" v-if="user.business" v-text="user.business.business_name"></div>
                                
                                <div class="w-full flex gap-4">
                                    
                                    <button class="btn btn-sm btn-light btn-flex btn-center gap-4" data-kt-follow-btn="true" v-if="user.id == auth.id || auth.role_id == 1" @click="showEditSide = true; showAddSide = false; activeItem = user">
                                        <vue-feather class="w-8" type="edit"></vue-feather>
                                        <span class="indicator-label" v-text="translate('Edit')"></span>
                                    </button>
                                    <div class="w-full"></div>
                                    <div class="mb-4 flex gap gap-2 cursor-pointer flex-end" @click="setActiveStatus(user)">
                                        <span :class="!user.active ? 'bg-inverse-dark' : ''" class="mt-1 bg-red-400 block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: user.active ? '16px' : 0}"></a></span>
                                        <span  v-text="user.active ? translate('Active') : translate('Pending')" class=" font-semibold inline-flex items-center px-2 py-1 rounded-full text-xs font-medium "></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <side-form-create @callback="closeSide" :conf="conf" model="User.create" v-if="showAddSide && content && content.fillable" :columns="content.fillable"  />

        <side-form-update @callback="closeSide" :conf="conf" model="User.update" :item="activeItem" :model_id="activeItem.id" index="id" v-if="showEditSide && !showAddSide " :columns="content.fillable"  />

    </main>
</template>
<script>

import {defineAsyncComponent, ref} from 'vue';
import {translate, handleGetRequest, handleRequest, deleteByKey, showAlert} from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
  import('@/components/includes/side-form-update.vue')
);

const user = defineAsyncComponent(() =>
  import('@/components/user.vue')
);


export default {
    
    components: {
        SideFormCreate,
        SideFormUpdate,
        user
        
    },  
    name: 'Users',
    setup(props) {

        const url =  props.conf.url+props.path+'?load=json';

        const showUser = ref(false);
        const showAddSide = ref(false);
        const showEditSide = ref(false);
        const activeItem = ref({});
        const content =  ref({});

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
            });
        }
        
        load();
        
        const closeSide = () => {
            showAddSide.value = false;
            showEditSide.value = false;
            showUser.value = false;
        }

        const setActiveStatus = (user) => {
            user.active = !user.active;
            var params = new URLSearchParams();
            params.append('type', 'User.updateStatus')
            params.append('params', JSON.stringify(user))
            handleRequest(params, '/api/update').then(response => {
                showAlert(response.result);
            })
        }
        
        const sameRole = (user, role) => 
        {
            if (user.role_id == role.role_id)
            {
                return true 
            } 
            return false;
        }
        
        return {
            url,
            showUser,
            showAddSide,
            showEditSide,
            content,
            activeItem,
            setActiveStatus,
            closeSide,
            sameRole,
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