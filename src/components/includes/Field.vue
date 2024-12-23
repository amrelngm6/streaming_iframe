<template>
    <div>
        <div class="media-library-field">
            <input v-if="file" :key="file" :name="name" type="hidden" :value="file">

            <div class="media-library-field__selector" v-if="content == null || content == ''">
                <span 
                     @click="showLibrary"
                    class="media-library-field__selector__button"
                >Attach {{ types.images && types.files ? 'file' : (types.images && !types.files) ? 'image' : 'file' }}</span>

            </div>
            <div v-if="file && content" :key="file" class="media-library-field__selected">
                <div class="media-library-field__selected__inner">
                    <div class="w-full">
                        <div>
                            <img :src="file" style="width: auto; height: auto; max-width: 100px;">
                        </div>
                        <div class="block w-full">
                            <div class="w-full flex" style="  margin: 2rem -0.5rem 0 -0.5rem;">
                                <div style="flex-grow: 1; padding: 0 0.5rem;">
                                    <span class="media-library-field__selected__inner__details__button font-semibold" @click="showLibrary"  v-text="translate('Edit')"></span>
                                </div>
                                <!-- <div style="flex-grow: 1; padding: 0 0.5rem;">
                                    <a :href="file.download_url" class="media-library-field__selected__inner__details__button">Download</a>
                                </div> -->
                                <div style="flex-grow: 1; padding: 0 0.5rem;">
                                    <button @click="clear" class="media-library-field__selected__inner__details__button media-library-field__selected__inner__details__button--delete"  v-text="translate('Remove')"></button>
                                </div>
                            </div>
                        </div>


                        <!-- <p v-if="helper" v-html="helper" class="media-library-field__selected__inner__details__helper" /> -->
                    </div>
                </div>
            </div>
        </div>


        <vue-medialibrary-manager
            :key="showManager"
            :api_url="api_url"
            :filetypes="filetypes"
            v-if="showManager"
            :types="types"
            :selected="value"
            :selectable="true"
            @close="showManager = false, file = content = value"
            @select="insert"
            @fail-to-find="clear"
        ></vue-medialibrary-manager>
    </div>
</template>


<script>
import {ref} from 'vue';
import Loader from '@/components/includes/Loader.vue';
import Manager from '@/components/includes/Manager.vue';
import {translate} from '@/utils.vue';

export default 
{
    name: 'vue-medialibrary-field',

    components: {
        'vue-medialibrary-manager': Manager,
        'app-medialibrary-loader': Loader
    },

    props: {
        name: {
            type: String,
            required: false
        },
        api_url: {
            type: String,
            required: false
        },
        filepath: {
            type: Object|String,
            required: false,
            default: () => ({
            })
        },
        types: {
            type: Object,
            required: false,
            default: () => ({
                images: true,
                files: true
            })
        },
        filetypes: {
            type: Array,
            required: false,
            default: () => ([])
        },
        helper: {
            type: String,
            required: false
        }
    },
    emits: ['changed', 'clear'],
    setup(props, {emit}) 
    {

        const showManager = ref();
        const file = ref();
        const content = ref();

        const showLibrary = () => 
        {
            showManager.value = !showManager.value;
        }

        const insert = (value) => {

            showManager.value = false;
            
            file.value = value.file_name;
            content.value = value.file_name;

            change();
        }

        const clear = () => {
            content.value = file.value = null;
        }

        
        const remove = () => {
            emit('clear', null);
        }

        const change = () => {
            emit('changed', file.value);
        }

        content.value = props.filepath;
        file.value = props.filepath;

        return {

            insert,
            clear,
            remove,
            change,
            showLibrary,
            translate,
            showManager,
            file,
            content,
        }
    },
    
}
</script>

