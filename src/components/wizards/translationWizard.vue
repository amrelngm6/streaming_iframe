<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">

            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />

            <div class=" card w-full py-10">
                <div class="w-full stepper stepper-links ">
                    <div class="stepper-nav justify-content-center py-2 ">
                        <div class="stepper-item " v-for="row in fillable" @click="activeTab = row">
                            <h3 :class="activeTab == row ? 'text-danger border-danger' : 'text-gray-400 border-transparent'"
                                class="cursor-pointer pb-3 px-2 stepper-title text-md border-b " v-text="translate(row)"></h3>
                        </div>
                    </div>
                    <div class="w-full">
                        <form :action="'/api/'+(activeItem.translation_id > 0 ? 'update' : 'create')" v-if="activeTab == 'Info'" :key="activeTab">
                            <input type="hidden" name="type" :value="activeItem.translation_id > 0 ? 'Translation.update' : 'Translation.create'" />
                            <input type="hidden" name="params[code]" :value="activeItem.code ?? ''" />
                            <input type="hidden" name="params[translation_id]" :value="activeItem.translation_id ?? ''" />
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="max-w-xl mb-6 mx-auto">

                                        <div
                                            class="notice d-flex bg-blue-100 rounded border-primary border border-dashed rounded-3 p-6">
                                            <div class="d-flex flex-stack flex-grow-1 ">
                                                <div class=" fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold"
                                                        v-text="translate('Translate the code')"></h4>
                                                    <div class="fs-6 text-gray-700 "
                                                        v-text="translate('Translate the word into the available languages')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="max-w-xl mb-6 mx-auto row">
                                            <label class="col-lg-4 col-form-label required fw-semibold fs-6" :for="'input' + i"
                                                v-text="translate('Code')"></label>
                                            <input :disabled="true" autocomplete="off" 
                                                class="form-control form-control-solid" :placeholder="translate('Generated from english translation')"
                                                v-model="activeItem.code">
                                        </div>

                                        <hr class="opacity-10 my-4" />
                                        <div class="w-full mb-6 " v-if="languages">
                                            <div class="w-full mb-6 mx-auto flex gap-4" v-for="(language, key) in languages">
                                                <label v-if="languages[key]" class="flex gap-4 cursor-pointer w-full col-form-label required fw-semibold fs-6">
                                                    <img class="rounded-full w-10 h-10" :src="language.icon" />
                                                    <p v-text="language.name" class="fw-bold fs-4"></p>
                                                </label>
                                                
                                                <input  autocomplete="off" class="form-control form-control-solid" :placeholder="translate('Translate into')+' '+translate(language.name)" :name="'params[translation]['+language.language_code+']'" v-model="fields[language.language_code]" >
                                            </div>
                                                
                                        </div>

                                    </div>
                                </div>
                            </div>
                            
                            
                            <p class="text-center mt-10"><button
                                    class="uppercase px-4 py-3 mx-2 text-center text-white rounded-lg bg-danger"
                                     v-text="translate('Submit')"></button></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';
import route_icon from '@/components/svgs/route.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, ref } from 'vue';
import { translate, getProgressWidth, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-update.vue')
);

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);
import field from '@/components/includes/Field.vue';

export default
    {
        components: {
            'vue-medialibrary-field': field,
            'datatabble': Vue3EasyDataTable,
            SideFormCreate,
            SideFormUpdate,
            close_icon,
            delete_icon,
            car_icon,
            route_icon,
            form_field,
        },
        name: 'Translations',
        emits: ['callback'],
        setup(props, { emit }) {

            const showEditSide = ref(false);
            const fields = ref([]);
            const activeItem = ref({translations:props.languages});
            const activeTab = ref('Info');
            const content = ref({});
            const fillable = ref(['Info']);

            if (props.item) {
                activeItem.value = props.item
                fields.value = props.item.translation ?? []
            }

            const back = () => {
                emit('callback');
            }

            const progressWidth = () => {
                let requiredData = ['name', 'description',  'double_cost_month', 'double_cost_quarter', 'double_cost_year', 'status'];

                return getProgressWidth(requiredData, activeItem);
            }

            const switchField = (field, key) => {
                console.log(field, key)
                activeField.value = key;
                showModal.value = true
            }
            const addField = () => {
                if (!activeItem.value.fields)
                {
                    activeItem.value.fields = [{'title': ''}];
                } else {
                    activeItem.value.fields.push({'title':''})
                }
                
                activeField.value = activeItem.value.fields.length - 1;
                showModal.value = true
            }

            const activeField = ref(0);
            const showModal = ref(false);
            
            return {
                fields,
                showModal,
                switchField,
                addField,
                activeField,
                progressWidth,
                showEditSide,
                content,
                fillable,
                activeItem,
                activeTab,
                translate,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            
            'setting',
            'item',
            'languages',
        ],

    };
</script>