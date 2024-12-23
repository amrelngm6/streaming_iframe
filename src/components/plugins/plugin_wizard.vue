<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">
            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
            <div id="kt_app_content_container" class="app-container  container-xxl ">
                <div class="action form d-flex flex-column flex-lg-row">

                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                        
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
                                <form_field :item="activeItem"
                                    :column="{required: true, key:'status',title: translate('status') , column_type:'select', text_key: 'title', column_key: 'value', data:[{'value': null,'title':translate('Pending')} , {'value':'on','title':translate('Active')}]}">
                                </form_field>
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
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" v-if="activeTab == translate('General')">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <div class="card card-flush py-4">
                                        <div class="card-header flex flex-nowrap">
                                            <div class="card-title w-full">
                                                <h2 v-text="translate('General')"></h2>
                                            </div>
                                            <ul class="flex gap-10 flex-none">
                                                <li class="text-lg flex py-5 gap-2 cursor-pointer"
                                                    @click="seoLang = langKey.language_code" v-for=" langKey in langs">
                                                    <img :src="langKey.icon"
                                                        class="rounded-full w-10 h-10 border-1 border border-gray-600 p-1" />
                                                    <a href="javascript:;"
                                                        :class="langKey.language_code == seoLang ? 'text-danger font-bold' : 'opacity-75 text-gray-400'"
                                                        v-text="langKey.name"></a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="w-full">
                                            <div v-for="language in langs" :key="seoLang">
                                                <div class="card-body pt-0" v-if="seoLang == language.language_code">
                                                    
                                                    <div class="mb-10 fv-row">
                                                        <label class="required form-label "><span
                                                                v-text="translate('Name')"></span><strong class="px-4"
                                                                v-text="language.name"></strong></label>
                                                        <input type="text" class="form-control mb-2"
                                                            v-model="activeItem.content_langs[language.language_code].title" />
                                                        <div class="text-muted fs-7"
                                                            v-text="translate('Name is required and recommended to be unique')">
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row">
                                                        <label class="form-label gap-6 flex"><span
                                                                v-text="translate('Description')"></span><strong
                                                                class="px-4" v-text="language.name"></strong> </label>
                                                        <ckeditor
                                                            v-model="activeItem.content_langs[language.language_code].short"
                                                            :editor="editor" :config="editorConfig" />
                                                        <div class="text-muted fs-7"
                                                            v-text="translate('Set a description to the item for better visibility')">
                                                        </div>
                                                    </div>
                                                    <div class="mb-10 fv-row">
                                                        <label class="form-label gap-6 flex"><span
                                                                v-text="translate('Page Content')"></span><strong
                                                                class="px-4" v-text="language.name"></strong> </label>
                                                        <ckeditor
                                                            v-model="activeItem.content_langs[language.language_code].content"
                                                            :editor="editor" :config="editorConfig" />
                                                        <div class="text-muted fs-7"
                                                            v-text="translate('Set a description to the item for better visibility')">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade show active" v-if="activeTab == translate('Options')">
                                <div class="d-flex flex-column gap-7 gap-lg-10">

                                    <div class="w-full">

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/admin/plugins" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5"
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
            Ckeditor,
            close_icon,
            delete_icon,
            form_field,
        },
        name: 'Plugins',
        emits: ['callback'],
        setup(props, { emit }) {


            const showAddCategory = ref(false);
            const activeItem = ref({
                "plugin": '',
                "position": '',
                "status": ''
            });

            const activeTab = ref(translate('General'));
            const seoLang = ref('english');
            const content = ref({});
            const collapsed = ref(false);
            
            const templates = ref([{title:  translate('Default'), value:'default'}, {title:  translate('Modern'), value:'modern'}]);

            const tabs = ref([translate('General'), translate('Options')]);

            const url =  props.conf.url+props.path+'?load=json';
            
            function load()
            {
                handleGetRequest( url ).then(response=> {
                    var parsedResponse  = JSON.parse(JSON.stringify(response))
                    if (parsedResponse.item) {
                        activeItem.value = parsedResponse.item ?? activeItem.value
                        
                    }
                    content.value  = parsedResponse 
                });
                
            }
            
            load();
            

            const closeSide = () => {
                load()
                showAddCategory.value  = false;
            }

            const save = () => {
                var params = new URLSearchParams();
                let array = JSON.parse(JSON.stringify(activeItem.value));
                let keys = Object.keys(array)
                let k, d, value = '';
                for (let i = 0; i < keys.length; i++) {
                    k = keys[i]
                    d = (typeof array[k] === 'object' || typeof array[k] === 'array' )? JSON.stringify(array[k]) : array[k]
                    params.append('params[' + k + ']', d)
                }

                let type = array.id > 0 ? 'update' : 'create';
                params.append('type', 'Plugin.' + type)
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


            return {
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