<template>
    <div class="w-full card mb-5 mb-xl-10" id="kt_profile_details_view">

        <div class="card-body p-9" v-if="activeItem.business">
            <div class="row my-4 py-4 border-b border-gray-200" >
                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Business name')"></label>
                <div class="col-lg-8">
                    <span class="fw-bold fs-6 text-gray-800" v-text="activeItem.business.business_name"></span>
                </div>
            </div>
            <div class="row my-4 py-4 " >
                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Business type')"></label>
                <div class="col-lg-8">
                    <span class="fw-bold fs-6 text-gray-800" v-text="activeItem.business.type"></span>
                </div>
            </div>
        </div>
                    
    </div>  
                
</template>
<script>

import { ref } from 'vue';
import { translate } from '@/utils.vue';

export default {

    components: {
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
        'overview',
    ]
};
</script>