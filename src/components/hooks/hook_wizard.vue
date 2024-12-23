<template>
    <div class="w-full overflow-auto">
        <div class="bg-danger border border-dashed border-warning d-flex mb-10 mx-4 notice p-6 rounded text-white">
                <!--begin::Icon-->
                <i class="ki-duotone ki-information fs-2tx text-warning me-4"><span class="path1"></span><span
                        class="path2"></span><span class="path3"></span></i> <!--end::Icon-->

                <!--begin::Wrapper-->
                <div class="d-flex flex-stack flex-grow-1 ">
                    <!--begin::Content-->
                    <div class=" fw-semibold">
                        <h4 class="text-gray-900 fw-bold" v-text="translate('How to use Hook')"></h4>

                        <div class="fs-6 text-gray-700 ">{{translate('Go to')}} <a class="fw-bold text-bg-warning" href="/admin/pages" v-text="translate('Frontend Pages')"></a> {{ translate('open the page content editor and add this code')}} <span class="fw-bold" v-text='"[plugin_shortcode id="+activeItem.id+" ] "'></span> .
                        </div>
                    </div>
                    <!--end::Content-->

                </div>
                <!--end::Wrapper-->
            </div>
        <div class=" w-full relative">
            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />


            <div id="kt_app_content_container" class="app-container  container-xxl ">
                <div class="action form d-flex flex-column flex-lg-row">

                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2 v-text="translate('Title')"></h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <input type="text" class="form-control mb-2" v-model="activeItem.title" />
                                <div class="text-muted fs-7" v-text="translate('Set the item title')"></div>
                            </div>
                        </div>


                        
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2 v-text="translate('Status')"></h2>
                                </div>
                                <div class="card-toolbar">
                                    <div :class="activeItem.status == 'on' ? 'bg-success' : 'bg-danger'"
                                        class="rounded-circle w-15px h-15px"></div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <select  v-model="activeItem.status"  class="form-control form-control-solid"   :placeholder="translate('Status')">
                                    <option value=""  v-text="translate('Pending')"></option>
                                    <option value="on"  v-text="translate('Active')"></option>
                                </select>
                                <div class="text-muted fs-7" v-text="translate('Set the item status')"></div>
                            </div>
                        </div>


                    </div>
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <ul
                            class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                            <li class="nav-item" v-for="tab in tabs">
                                <a class="nav-link text-active-primary pb-4 " @click="activeTab = tab"
                                    :class="tab == activeTab ? 'active' : ''" href="javascript:;" v-text="tab"></a>
                            </li>
                            <li class="nav-item" v-for="(tab, key) in content.fillable">
                                <a class="nav-link text-active-primary pb-4 " @click="activeTab = key"
                                    :class="key == activeTab ? 'active' : ''" href="javascript:;"
                                    v-text="translate(key)"></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" v-if="activeTab == translate('Plugin')">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <div class="card card-flush py-4">
                                        <div class="card-header flex flex-nowrap">
                                            <div class="card-title w-full">
                                                <h2 v-text="translate('Plugin')"></h2>
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10" style="background-size: auto calc(100% + 10rem); background-position-x: 100%; background-image: url('/uploads/img/4.png')">

                                                <!--begin::Card header-->
                                                <div class="card-header pt-10">
                                                    <div class="d-flex align-items-center">

                                                        <!--begin::Title-->
                                                        <div class="d-flex flex-column" v-if="activeItem.plugin">
                                                            <h2 class="mb-1" v-text="activeItem.plugin ? activeItem.plugin.name : ''"></h2>
                                                            <div class="text-muted fw-bold">
                                                                <a href="#">Medians</a> <span class="mx-3">|</span v-if="activeItem.plugin.plugin"> {{activeItem.plugin.plugin.version}} V <span class="mx-3">|</span> {{ translate('status') }} {{activeItem.status}}
                                                            </div> 
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                </div>
                                                <!--end::Card header-->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane fade show active" v-if="activeTab != translate('Plugin')">
                                <div class="card w-full ">

                                    <div class="card-body w-full">
                                        <div class="py-1 w-full pt-4" v-for="column in content.fillable[activeTab]">
                                            <span class="block mb-2 form-label text-gray-600 text-lg"
                                                v-text="column.title" v-if="column.column_type != 'hidden'"></span>
                                            <form_field @callback="handleField" :column="column" 
                                                :item="activeItem.field" :conf="conf"></form_field>
                                            <!-- <select_field @callback="handleField" :column="column" v-if="column.type == 'select'"
                                                :item="activeItem.field" :conf="conf"></select_field> -->
                                            <p v-text="column.help_text" v-if="column.help_text"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/admin/hooks" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5"
                                v-text="translate('Cancel')"></a>
                            <button @click="save" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                <span class="indicator-label" v-text="translate('Save Changes')"> </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>

import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';
import field from '@/components/includes/Field.vue';
import static_field from '@/components/includes/static_form_field.vue';
import select_field from '@/components/includes/select_field.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, computed, ref } from 'vue';
import { translate, handleGetRequest, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);
import color_picker from '@/components/includes/color-picker.vue';



import { ClassicEditor, Bold, Essentials, Italic, Mention, Paragraph, Link, List, Table, TableToolbar, Image, Undo, Heading, Font } from 'ckeditor5';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';

import 'ckeditor5/ckeditor5.css';


export default
    {
        components: {
            'datatabble': Vue3EasyDataTable,
            'vue-medialibrary-field': field,
            color_picker,
            static_field,
            select_field,
            Ckeditor,
            close_icon,
            delete_icon,
            form_field,
        },
        name: 'Hooks',
        emits: ['callback'],
        setup(props, { emit }) {


            const showAddCategory = ref(false);
            const activeItem = ref({
                "field": {},
                "hook": '',
                "position": '',
                "status": ''
            });

            const activeTab = ref(translate('Plugin'));
            const activeOptionTab = ref(translate('Basic'));
            const seoLang = ref('english');
            const content = ref({});
            const collapsed = ref(false);

            const templates = ref([{ title: translate('Default'), value: 'default' }, { title: translate('Modern'), value: 'modern' }]);

            const tabs = ref([translate('Plugin')]);

            const url = props.conf.url + props.path + '?load=json';

            function load() {
                handleGetRequest(url).then(response => {
                    var parsedResponse = JSON.parse(JSON.stringify(response))
                    if (parsedResponse.item) {
                        activeItem.value = parsedResponse.item ?? activeItem.value

                    }
                    content.value = parsedResponse
                });

            }

            load();


            const closeSide = () => {
                load()
                showAddCategory.value = false;
            }

            const save = () => {
                var params = new URLSearchParams();
                let array = JSON.parse(JSON.stringify(activeItem.value));
                let keys = Object.keys(array)
                let k, d, value = '';
                for (let i = 0; i < keys.length; i++) {
                    k = keys[i]
                    d = (typeof array[k] === 'object' || typeof array[k] === 'array') ? JSON.stringify(array[k]) : array[k]
                    params.append('params[' + k + ']', d)
                }

                let v,data;
                Object.keys(activeItem.value.field).forEach((e, i) => {
                    v = activeItem.value.field[e];
                    data = (typeof v === 'object' || typeof v === 'array') ? JSON.stringify(v) : v
                    params.append('params[options]['+e+']',  data)
                })

                let type = array.id > 0 ? 'update' : 'create';
                params.append('type', 'Hook.' + type)
                handleRequest(params, '/api/' + type).then(response => {
                    handleAccess(response)
                })
            }


            const back = () => {
                emit('callback');
            }

            const selectedObject = (array, key) => {
                const keyValue = activeItem.value[key];
                for (let i = 0; i < array.length; i++) {
                    if (array[i][key] == keyValue) {
                        return array[i]
                    }
                }
                return {}
            }



            const getLang = (val, index) => {
                activeItem.value.content_langs[index] = val.content_langs[index] ?? {}
                return activeItem.value.content_langs[index]
            }

            const handleField = (val, index, append) => {
                console.log(val, index)
                if (append) {
                    activeItem.value.field[index] = val
                }
                // activeItem.value.field[index] = val
            }
            
            const switchStatus = (val, index) => {
                console.log(val)
                console.log(index)

            }
                
                
            return {
                switchStatus,
                activeOptionTab,
                handleField,
                getLang,
                selectedObject,
                closeSide,
                content,
                templates,
                tabs,
                activeItem,
                activeTab,
                translate,
                collapsed,
                seoLang,
                save,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            'business_setting',
            'langs',
            'setting',
            'currency',
            'item'
        ],

    };
</script>

<style>
textarea {
    width: 100%;
    height: 200px;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.preview {
    margin-top: 20px;
}

.preview h2 {
    margin-bottom: 10px;
}

.preview div {
    padding: 10px;
    border-radius: 4px;
}
</style>