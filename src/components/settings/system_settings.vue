<template>
    <div class="w-full " v-if="content">

        <div class="card mb-9">
            <div class="card-body pt-9 pb-0">
                
                <div class="d-flex align-items-center mb-1" v-if="item">
                    <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3" v-text="content.title"></a>
                    <span class="badge badge-light-success me-auto"></span>
                </div>
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li v-for="(row,k) in content.fillable" class="nav-item">
                        <a @click="switchTab(k)" v-text="handleTabName(k)" class="nav-link text-active-primary py-5 me-6 " :class="activeTab == k ? 'active' : ''"  href="javascript:;"></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=" w-full">
            <form action="/api/update" method="POST" data-refresh="1" id="system-setting-form" class="action  px-4 m-auto rounded-lg pb-10" >

                <input name="type" type="hidden" value="SystemSettings.update">

                <div class="w-full "  v-for="(row,k) in content.fillable">
                    <div class="card w-full py-10" v-if="activeTab == k">
                        <div class="card-body pt-0"  >
                            <div class="settings-form" >
                                <div class="row mb-6"   v-for="(field, i) in row" >
                
                                    <label class="col-lg-4 col-form-label " :class="field.required ? 'required' : ''" :for="'input'+i"  v-if="field.column_type != 'hidden'">
                                        <p class="fw-bold fs-4 w-full" v-text="field.title" ></p>
                                        <p v-text="field.help_text" v-if="field.help_text" ></p>
                                    </label>

                                    <form_field class="col-lg-8 fv-row fv-plugins-icon-container" :callback="closeSide" :column="field" :model="model"  :item="item" :conf="conf"></form_field>
                                                      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <button class="uppercase mt-3 text-white mx-auto rounded-lg bg-purple-800 hover:bg-red-800 px-4 py-2">{{translate('Save')}}</button>
            </form>
        </div>
                   
    </div>
</template>
<script>

import {ref } from 'vue';
import {translate,handleGetRequest,isInput , handleTabName } from '@/utils.vue';
import field from '@/components/includes/Field.vue';
import form_field from '@/components/includes/form_field.vue';

export default 
{
    components: {
        'vue-medialibrary-field': field,
        form_field,
        translate
    },
    name:'Settings',
    setup(props) {
        
        const url =  props.conf.url+props.path+'?load=json';
                
        const content =  ref({
            title: '',
            settings: [],
        });
        
        const item =  ref({});

        const load = () => {
            handleGetRequest( url ).then(response=> {
                content.value = JSON.parse(JSON.stringify(response))
                item.value = content.value.setting;
            });
        }
        
        load();

        const activeTab =  ref('basic');

        const switchTab = (tab) => 
        {
            activeTab.value = tab;
        }

        
        const setActiveStatus = (item, key) => {
            item[key] = !item[key];
        }      

        const handleName = (column) => {
            return  (column && column.custom_field) ? 'params[field]['+column.key+']' : 'params['+column.key+']';
        }


        return {
            url,
            content,
            item,
            isInput,
            setActiveStatus,
            handleTabName,
            handleName,
            switchTab,
            activeTab,
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
<style lang="css">
    .rtl #side-cart-container
    {
        right: auto;
        left:0;
    }
</style>