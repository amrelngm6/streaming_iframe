<template>
    <div class=" w-full" v-if="activeItem">
        <div class="grid xl:grid-cols-12 lg:grid-cols-12 grid-cols-1 gap-6" >
            <div class="xl:col-span-3 lg:col-span-5">
                <div class="card px-4 py-6 mb-6">
                    <div class="text-center pb-4" >
                        <h4 class="mb-6 mt-3 text-lg dark:text-gray-300" v-text="activeItem.name"></h4>
                        <button type="button" @click="close" class=" hover:bg-primary mb-3 px-6 py-2  text-danger inline-flex "
                            ><vue-feather class="px-2" type="x-circle"></vue-feather> <span class="pt-1" v-text="translate('Back')"></span></button>
                    </div>
                </div> <!-- end card -->
            </div>

            <div class="xl:col-span-9 lg:col-span-7">
                <div class="card">
                    <div class="p-6">
                        <div class="flex w-full ">
                            <div class="w-full ">
                                <h1 class="font-bold text-lg w-full" v-text="translate('Permissions list')"></h1>
                                <p v-text="translate('Click on the permission to update')"></p>
                            </div>
                            <div>
                                <button type="button" @click="update" class="bg-gray-50 border  border-1 hover:bg-primary mb-3 px-6 py-2 rounded-lg text-primary" v-text="translate('Save')"></button>
                            </div>
                        </div>
                        <div class="py-6 w-full" v-if="activeItem">
                            <nav class="max-w-xl space-y-3 bg-gray-100 p-4 dark:bg-gray-900/30"
                                aria-label="Tabs" role="tablist">
                                <label @click="setActiveStatus(permission)" :class="permission.access ? 'menu-dark  font-semibold' : 'text-gray-500'" v-for="permission in activeItem.permissions" 
                                class="text-lg cursor-pointer px-4 flex gap gap-4 mb-2 hover:bg-white hover:text-blue-800 hs-tab-active:font-semibold hs-tab-active:bg-white dark:hs-tab-active:bg-gray-700 w-full  py-2 rounded items-center gap-2 border-b-2 border-transparent -mb-px transition-all whitespace-nowrap dark:text-white active">
                                    <span :class="!permission.access ? 'bg-gray-200' : 'bg-red-400'" class=" block h-4 relative rounded-full w-8" style="direction: ltr;" ><a class="absolute bg-white block h-4 relative right-0 rounded-full w-4" :style="{left: permission.access ? '16px' : 0}"></a></span>
                                    <span type="button" v-text="translate(permission.model)"></span>
                                </label>
                                 <!-- button-end -->
                            </nav> <!-- nav-end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { ref} from 'vue';
import {translate} from '@/utils.vue';

export default
{
    components: {
        translate,
    },  
    emit: [
        'update', 'close'
    ],
    setup(props, {emit}) {

        const url =  props.conf.url+props.path+'?load=json';
        
        const activeItem = ref({});

        const content =  ref({
            title: '',
            items: [],
            columns: [],
        });

        const load = () => {
            activeItem.value = props.item;
        }
        
        load();

        const setActiveStatus = (permission) => {
            if (permission)
            {
                permission.access = !permission.access;
            }
        }


        const update = () =>
        {
            emit('save', 'save', activeItem.value);
        }

        const close = () => 
        {
            emit('close', 'close', activeItem.value);
        }

        return {
            url,
            content,
            activeItem,
            close,
            update,
            setActiveStatus,
            translate,
        };
    },

    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
        'item',
    ],
};


</script>