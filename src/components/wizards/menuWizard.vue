<template>
  <div class=" w-full relative">
      <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
      
      <div v-if="loader" :key="loader" class="bg-white fixed w-full h-full top-0 left-0" style="z-index:99999; opacity: .9;">
          <img class="m-auto w-500px" :src="'/uploads/loader.gif'" />
      </div>
      <h2 v-text="item.name"></h2>
      <p v-text="translate('Drag & Drop your link for menu') + ' ' + item.type"></p>

      <div class="flex gap-10 w-full py-10">
        
        <div class="w-full">
            <div class="w-full">
                <div class="w-full">
                    <input type="radio" class="btn-check" name="account_type" value="personal" checked="checked" id="kt_create_account_form_account_type_personal">
                    <label @click="allPages = pages, activeTab = 'pages'" class="gap-6 btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-10" for="kt_create_account_form_account_type_personal">
                        <vue-feather type="grid" />
                        <span class="d-block fw-semibold text-start">                            
                            <span class="text-gray-900 fw-bold d-block fs-4 mb-2 text-left" v-text="translate('Pages')"></span>
                            <span class="text-muted fw-semibold fs-6" v-text="translate('List of the front pages')"></span>
                        </span>
                    </label>   
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
                
                <div class="w-full">
                    <input type="radio" class="btn-check" name="account_type" value="corporate" id="kt_create_account_form_account_type_corporate">
                    <label @click="allPages = categories, activeTab = 'categories'" class="gap-6 btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="kt_create_account_form_account_type_corporate">
                        <vue-feather type="shopping-bag" />
                        <!--begin::Info-->
                        <span class="d-block fw-semibold text-start">                              
                            <span class="text-gray-900 fw-bold d-block fs-4 mb-2 text-left" v-text="translate('Categories')"></span>
                            <span class="text-muted fw-semibold fs-6" v-text="translate('List of the products categories')"></span>
                        </span>           
                    </label>           
                </div>
            </div>
        </div>
        <div>
          <VueDraggable class="shadow-sm shadow-sm flex flex-col gap-2 p-4 w-300px h-300px m-auto bg-white overflow-auto"
          v-model="allPages" animation="150" ghostClass="ghost" group="people" >
              <label v-for="page in allPages" :key="page"  class="align-items-center btn btn-active-light-primary btn-outline btn-outline-dashed d-flex" for="kt_create_account_form_account_type_corporate">
                <vue-feather :type="activeTab == 'pages' ? 'grid' : 'shopping-bag'"></vue-feather>
                  <i class="ki-duotone ki-element-8 fs-3x me-5"><span class="path1"></span><span class="path2"></span></i>
                  <!--begin::Info-->
                  <span class="d-block fw-semibold text-start w-100">                              
                      <span class="d-block fs-4 fw-bold mb-2 text-gray-900 text-left pt-2"> {{ page.name }}</span>
                      
                  </span>
                  <i @click="addMenu(page)"  class="fa fa-plus fs-1 px-2 py-2"></i>           
                  <!--end::Info-->               
              </label>
              
          </VueDraggable>
        </div>
        <div class=" flex-column-fluid  w-300px " >
          <div class="shadow-sm shadow-sm flex flex-col gap-2 p-4 w-300px h-300px m-auto bg-white overflow-auto" >
            
              <Draggable ref="tree" textKey="name" childrenKey="items" maxLevel="2"  v-model="treeData" treeLine @change="change">
                <template #default="{ node, stat }" >
                  <div class="cursor-move h-30 bg-gray-500/5 rounded p-3 my-1 flex">
                    <span class="mtl-ml w-full">{{ node.name }}</span>
                    <vue-feather type="delete" @click="remove(stat)" />
                  </div>
                </template>
              </Draggable>
          </div>
          <button @click="saveItem" id="kt_ecommerce_add_product_submit" class="mx-auto btn btn-primary"><span class="indicator-label" v-text="translate('Save Changes')"></span></button>
        </div>

      </div>
      
  </div>
</template>
<script>

import close_icon from '@/components/svgs/Close.vue';
import { handleAccess, handleRequest, translate } from '@/utils.vue';
import { ref } from 'vue';
import { VueDraggable } from 'vue-draggable-plus'


import { BaseTree, Draggable } from '@he-tree/vue'
import '@he-tree/vue/style/default.css'



export default
  {
    components: {
      Draggable  ,
      VueDraggable,
      close_icon
    },
    emits: ['close'],
    setup(props, {emit}) {

      const activeTab = ref('pages');
      const tree = ref(false);
      const loader = ref(false);
      const allPages = ref([])
      

      const selectedPages = ref([]);
      allPages.value = props.pages ??  [];
      
      const handleSelected = () => {
        let array = props.active_links;
        if (array.length < 1)
          return;
        
        for (let i = 0; i < array.length; i++) {
          const element = array[i].page;
          if (element) {
            element.type = array[i].type;
            element.items = array[i].items;
            if (element.type == props.item.type)
            {
              selectedPages.value[selectedPages.value.length] = element;
            }
          }
        }
      }
      
      handleSelected();

      const onUpdate = () => {
        saveItem();
      }
      const onAdd = () => {
        saveItem();
        console.log('add')
      }
      const change = () => {
        tree.value.openAll()

        console.log(tree.value.getData())
      }
      const remove = (stat) => {
        tree.value.remove(
          stat
        )
      }
      const back = () => {
        emit('close');
      }

      
      const saveItem = () => {
        console.log(selectedPages.value)
          loader.value = true;
          var params = new URLSearchParams();
          params.append('params[type]', props.item.type )
          params.append('params[items]', JSON.stringify(selectedPages.value.filter(e=> e != null)) )
          params.append('type', 'Menu.update' )
          handleRequest(params, '/api/update').then(response => {
              loader.value = false;
              handleAccess(response)
          })
      }
      
      const treeData = ref(selectedPages.value);
      
      const addMenu = (page, index=0) => {

          tree.value.add(
            page,
            index
          )
      }


      return {
        activeTab,
        change,
        tree,
        addMenu,
        treeData,
        loader,
        allPages,
        selectedPages,
        back,
        translate,
        saveItem,
        onUpdate,
        onAdd,
        remove
      };
    },
    props: [
      'path',
      'langs',
      'setting',
      'conf',
      'auth',
      'pages',
      'categories',
      'active_links',
      'item',
    ],
  };
</script>