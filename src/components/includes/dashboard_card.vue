<template>
    <div class=" ">
        <div :class="classes" class="card card-img-holder text-white h-100">
            <div class="card-body">
                <img src="/../uploads/img/circle.png" class="card-img-absolute" alt="circle-image">
                <h4 class="font-weight-normal mb-3 relative" v-text="title"></h4>
                <span class="text-2xl" v-text="value"></span>
            </div>
        </div>
    </div>
</template>
<script>

export default 
{
    props: [
        'title',
        'classes',
        'value',
    ],

};
</script>
