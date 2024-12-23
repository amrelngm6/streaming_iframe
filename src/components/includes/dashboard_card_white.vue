<template>
    
    <div :class="classes" class="card hoverable card-xl-stretch mb-xl-8">

        <!--begin::Body-->
        <div class="card-body">
            <img width="25" height="25" :src="icon" />

            <div :class="value_class" class="fw-bold fs-2 mb-2 mt-5"  v-text="value"> </div>

            <div :class="text_class" class="fw-semibold "  v-text="title"></div>
        </div>
        <!--end::Body-->
    </div>

</template>
<script>

export default 
{
    props: [
        'title',
        'value',
        'classes',
        'text_class',
        'value_class',
        'icon',
    ],

};
</script>
