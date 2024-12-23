<template>
    <div class="w-full " >
        <div class="card">
            <div class="card-body relative">
                <div class="card-px text-center pt-15 pb-15">
                    <h2 class="fs-2x fw-bold mb-0" v-text="translate('Choose user type')"></h2>
                    <p class="text-gray-400 fs-4 font-semibold py-7"
                        v-text="translate('Define the required user type to complete information')"></p>
                </div>

                <div class="pb-15 px-5">
                    <div class="relative mx-auto max-w-6xl py-4">
                        <div class="w-full row gap-10">
                            <div class="w-full col-3" v-if="auth && plugins" v-for="plugin in plugins" @click="setType(plugin)">
                                <input type="radio" class="btn-check" value="plugin_class"
                                    :checked="activeItem.plugin_class == plugin.class ? true : false"
                                    name="payment_type" />
                                <label class="gap-6 btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex  mb-10">
                                    <span class="d-block fw-semibold text-start">
                                        <span class="w-full flex gap-4">
                                            <vue-feather type="codesandbox"></vue-feather>
                                            <span class="text-gray-900 fw-bold d-block fs-4 mb-2"
                                                v-text="plugin.name"></span>
                                        </span>
                                        <span class="text-muted fw-semibold fs-6"
                                            v-text="plugin.description"></span>
                                    </span>
                                </label>
                            </div>
                            
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
    emits:['callback'],

    setup(props, {emit}) {

        const activeItem = props.item;
        
        const setType = (type) => {
            emit('callback', type)
        }

        return {
            setType,
            activeItem,
            translate,
        };
    },

    props: [
        'path',
        'lang',
        'system_setting',
        'setting',
        'setting',
        'conf',
        'auth',
        'item',
        'plugins',
        'alias'
    ],
    
};
</script>