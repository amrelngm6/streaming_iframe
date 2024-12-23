<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">
            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
            <div id="kt_app_content_container" class="app-container  container-xxl " v-if="activeItem" >
                <div class="action form d-flex flex-column flex-lg-row">
                    
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 min-w-80 w-lg-300px mb-7 me-lg-10">
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title"><h2 v-text="translate('Main picture')"></h2></div>
                            </div>
                            <div class="card-body text-center pt-0 ">

                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3 w-full" >
                                    <vue-medialibrary-field @changed="(val) => { activeItem.picture = val;}" :key="activeItem" name="params[picture]"
                                        :filepath="activeItem.picture ?? ''" v-if="conf"
                                        :api_url="conf.url"></vue-medialibrary-field>
                                </div>
                                <div class="text-muted fs-7" v-text="translate('allowed media types msg')"></div>
                            </div>
                        </div>
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title"> <h2 v-text="translate('Status')"></h2></div>
                                <div class="card-toolbar">
                                    <div :class="activeItem.status == 'on' ? 'bg-success' : 'bg-danger'" class="rounded-circle w-15px h-15px" ></div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <form_field :item="activeItem" :column="{required: true, key:'status',title: translate('status') , column_type:'select', text_key: 'title', column_key: 'value', data:[{'value': null,'title':translate('Pending')} , {'value':'on','title':translate('Active')}]}" ></form_field>
                                <div class="text-muted fs-7" v-text="translate('Set the item status')"></div>
                            </div>
                        </div>
                        
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title"> <h2 v-text="translate('Homepage')"></h2></div>
                                <div class="card-toolbar">
                                    <div :class="activeItem.homepage == 'on' ? 'bg-success' : 'bg-danger'" class="rounded-circle w-15px h-15px" ></div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <form_field :item="activeItem" :column="{required: true, key:'homepage',title: translate('homepage') , column_type:'select', text_key: 'title', column_key: 'value', data:[{'value': null,'title':translate('Sub-page')} , {'value':'on','title':translate('Homepage')}]}" ></form_field>
                                <div class="text-muted fs-7" v-text="translate('Set this page as Homepage')"></div>
                            </div>
                        </div>
                        

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title"><h2 v-text="translate('Page Template')"></h2></div>
                            </div>
                            <div class="card-body pt-0">
                                <label  v-text="translate('Select a item template')" class="form-label"></label>
                                <form_field :item="activeItem" :column="{required: true, key:'template',title: translate('Template') , column_type:'select', text_key: 'title', column_key: 'value', data: templates}" ></form_field>
                                <div class="text-muted fs-7" v-text="translate('Assign a template from your current theme to define how a single item is displayed')"></div>
                            </div>
                        </div>

                        
                        

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title"><h2 v-text="translate('CSS Classes')"></h2></div>
                            </div>
                            <div class="card-body pt-0" >
                                <label  v-text="translate('CSS custom class')" class="form-label"></label>
                                <form_field  @callback="(newVal) => {activeItem.field ? (activeItem.field.class = newVal) : (activeItem.field = newVal), console.log(newVal)}" :item="activeItem.field ?? {}" :column="{required: false, key:'class',title: translate('Class') , column_type:'text', column_key: 'class'}" ></form_field>
                                <div class="text-muted fs-7" v-text="translate('Assign one or more css class to this page to define the style')"></div>
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
                            <div class="tab-pane fade show active" v-if="activeTab == translate('General')" >
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <div class="card card-flush py-4">
                                        <div class="card-header flex flex-nowrap">
                                            <div class="card-title w-full"><h2 v-text="translate('General')"></h2></div>
                                            <ul class="flex gap-10 flex-none">
                                                <li class="text-lg flex py-5 gap-2 cursor-pointer"
                                                    @click="seoLang = language.language_code" v-for=" language in langs"><img
                                                        :src="language.icon"
                                                        class="rounded-full w-10 h-10 border-1 border border-gray-600 p-1" />
                                                    <a href="javascript:;"
                                                        :class="language.language_code == seoLang ? 'text-danger font-bold' : 'opacity-75 text-gray-400'"
                                                        v-text="language.language_code"></a></li>
                                            </ul>
                                        </div>
                                        <div class="w-full">
                                            <div v-for="language in langs" :key="seoLang">
                                                <div class="card-body pt-0" v-if="language && seoLang == language.language_code">
                                                    <div class="mb-10 fv-row">
                                                        <label class="required form-label "><span v-text="translate('Path')"></span><strong class="px-4" v-text="language.name"></strong></label>
                                                        <div class="position-relative flex">
                                                            <input :disabled="true" :value="conf.url" class="bg-gray-200 form-control-solid px-4"  />
                                                            <input type="text" v-model="getLang(activeItem, language.language_code).prefix" class="form-control form-control-solid"  />
                                                        </div>
                                                        <div class="text-muted fs-7" v-text="translate('URL path for the item webpage')"> </div>
                                                    </div>
                                                    <div class="mb-10 fv-row">
                                                        <label class="required form-label "><span v-text="translate('Name')"></span><strong class="px-4" v-text="translate(language.language_code)"></strong></label>
                                                        <input type="text" class="form-control mb-2" v-model="activeItem.content_langs[language.language_code].title" />
                                                        <div class="text-muted fs-7" v-text="translate('Name is required and recommended to be unique')"> </div>
                                                    </div>
                                                    <div>
                                                        <label class="form-label gap-6 flex"><span v-text="translate('Content')"></span><strong class="px-4" v-text="translate(language.language_code)"></strong> </label>
                                                        <form_field :column="{column_type: 'editor', key: 'content'}" :item="activeItem.content_langs[language.language_code]" :conf="conf"></form_field>
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
                            
                            <div class="tab-pane fade show active" v-if="activeTab == translate('SEO')" >
                                <div class="d-flex flex-column gap-7 gap-lg-10">

                                    <div class="w-full">
                                        <div v-for="tabLanguage in langs">

                                            <div :key="tabLanguage" class="card card-flush py-4" v-if="seoLang == tabLanguage.language_code">
                                                <div class="card-header flex flex-nowrap">
                                                    <div class="w-full card-title"><h2 v-text="translate('Meta Options')"></h2></div>
                                                    <ul class="flex gap-10 flex-none">
                                                        <li class="py-5 " v-for="language in langs" >
                                                            <div v-if="key" class="text-lg flex gap-2 cursor-pointer" @click="seoLang = language.language_code" >
                                                                <img :src="language.icon" class="rounded-full w-10 h-10 border-1 border border-gray-600 p-1" />
                                                                <a href="javascript:;" :class="language.language_code == seoLang ? 'text-danger font-bold' : 'opacity-75 text-gray-400'" v-text="language.name"></a>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="card-body pt-0" v-if="tabLanguage">
                                                    <div class="mb-10">
                                                        <label class="form-label" v-text="translate('Meta Tag Title') + ' (' + tabLanguage.name + ')'"></label>
                                                        <input type="text" class="form-control mb-2" name="meta_title"
                                                            :placeholder="translate('Meta tag name')" v-model="activeItem.content_langs[tabLanguage.language_code].seo_title" />
                                                        <div class="text-muted fs-7" v-text="translate('Set a meta tag title. Recommended to be simple and precise keywords') + ' (' + tabLanguage.name + ')'"></div>
                                                    </div>

                                                    <div class="mb-10">
                                                        <label class="form-label" v-text="translate('Meta Tag Description')"></label>
                                                        <textarea v-model="activeItem.content_langs[tabLanguage.language_code].seo_desc" class=""></textarea>
                                                        <div class="text-muted fs-7" v-text="translate('Set a meta tag description to the item for increased SEO ranking')"></div>
                                                    </div>

                                                    <div>
                                                        <label class="form-label" v-text="translate('Meta Tag Keywords')"></label>
                                                        <input id="kt_ecommerce_add_item_meta_keywords" class="form-control" v-model="activeItem.content_langs[tabLanguage.language_code].seo_keywords" />
                                                        <div class="text-muted fs-7" v-text="translate('Set a list of keywords that the item is related to. Separate the keywords by adding a comma <code>,</code> between each keyword')"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="javascript:;" @click="back" class="btn btn-light me-5" v-text="translate('Cancel')"></a>
                            <button @click="save" class="btn btn-primary">
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
import route_icon from '@/components/svgs/route.vue';
import car_icon from '@/components/svgs/car.vue';
import field from '@/components/includes/Field.vue';
import static_field from '@/components/includes/static_form_field.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, toRaw , ref } from 'vue';
import { translate, handleGetRequest, handleRequest, deleteByKey, showAlert, handleAccess, getPositionAddress, findPlaces, getPlaceDetails } from '@/utils.vue';

const SideFormCreate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-create.vue')
);

const SideFormUpdate = defineAsyncComponent(() =>
    import('@/components/includes/side-form-update.vue')
);

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);
import Vue3TagsInput from 'vue3-tags-input';
import color_picker from '@/components/includes/color-picker.vue';


export default
    {
        components: {
            'datatabble': Vue3EasyDataTable,
            'vue-medialibrary-field': field,
            color_picker,
            static_field,
            Vue3TagsInput,
            SideFormCreate,
            SideFormUpdate,
            close_icon,
            delete_icon,
            car_icon,
            route_icon,
            form_field,
        },
        name: 'Pages',
        emits: ['callback'],
        setup(props, { emit }) {


            const showAddPage = ref(false);
            const showEditSide = ref(false);
            const activeItem = ref({
                "page_id": 0,
                "picture": '/uploads/img/placeholder.png',
                "content_langs": {},
            });
            const seoLang = ref('english');
            const categories = ref([]);
            const activeTab = ref(translate('General'));
            const content = ref({});
            const collapsed = ref(false);

            const templates = ref([{title:  translate('Default'), value:'subpage'}, {title:  translate('Fullwidth'), value:'fullwidth'}]);

            const tabs = ref([translate('General'), translate('SEO')]);

            const url =  props.conf.url+props.path+'?load=json';

            const load = () =>
            {
                handleGetRequest( url ).then(response=> {
                    var parsedResponse  = JSON.parse(JSON.stringify(response))
                    activeItem.value = parsedResponse.item ?? activeItem.value;
                    content.value  = parsedResponse 
                    categories.value = content.value.categories 
                });
                
            }
            
            load();
            

            const closeSide = () => {
                load()
                showAddPage.value  = false;
            }

            const save = () => {
                // var params = new URLSearchParams();
                // let array = JSON.parse(JSON.stringify(activeItem.value));
                // let keys = Object.keys(array)
                // let k, d, value = '';
                // for (let i = 0; i < keys.length; i++) {
                //     k = keys[i]
                //     d = (typeof array[k] === 'object' || typeof array[k] === 'array' )? JSON.stringify(array[k]) : array[k]
                //     params.append('params[' + k + ']', d)
                // }
                const finalObject = toRaw(activeItem.value);
                
                finalObject.field = activeItem.value.field ? {class: activeItem.value.field.class} : {}
                
                var params = new URLSearchParams();
                params.append('params', JSON.stringify(finalObject))

                let type = (activeItem.value.page_id && activeItem.value.page_id > 0 ) ? 'update' : 'create';
                let model = content.value.model ?? 'Page';
                params.append('type', model + '.' + type)
                handleRequest(params, '/api/' + type).then(response => {
                    handleAccess(response)
                })
            }


            const back = () => {
                emit('callback');
            }


            const getLang = (val, index) => {
                activeItem.value.content_langs[index] = val.content_langs[index] ?? {}
                return activeItem.value.content_langs[index]
            }


            return {
                getLang,
                showAddPage,
                showEditSide,
                closeSide,
                content,
                templates,
                tabs,
                categories,
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