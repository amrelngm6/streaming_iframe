<template>

    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view" >
        <div class="card-body p-9">
            <form action="/api/update" method="POST" data-refresh="1" id="system-setting-form"
                class="action  px-4 m-auto rounded-lg pb-10">

                <input name="type" type="hidden" value="User.update">

                <div class="w-full " v-for="(field, i) in fillable">
                    <div class="card w-full " v-if="field.key != 'role_id'">
                        <div class="card-body pt-0">
                            <div class="settings-form">
                                <div class="row mb-6">

                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6" :for="'input' + i"
                                        v-text="field.title" v-if="field.column_type != 'hidden'"></label>
                                    <div class="col-lg-8 fv-row fv-plugins-icon-container">
                                        <form_field :callback="closeSide" :column="field" :model="null"
                                            :item="activeItem" :conf="conf"></form_field>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="uppercase mt-3 text-white mx-auto rounded-lg bg-purple-800 hover:bg-red-800 px-4 py-2">{{
                    translate('Save') }}</button>
            </form>

        </div>
    </div>
</template>
<script>

import { defineAsyncComponent, ref } from 'vue';
import { translate } from '@/utils.vue';
const form_field = defineAsyncComponent(() => import('@/components/includes/form_field.vue') );

export default {

    components: {
        form_field
    },
    setup(props) {

        const activeItem = ref({});

        activeItem.value = props.item;

        return {
            activeItem,
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
        'item',
        'fillable',
    ]
};
</script>