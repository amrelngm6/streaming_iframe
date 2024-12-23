<template>
    <div style="transform:translate(-50%, -50%)" class="absolute w-full rounded-lg bg-gray-900 text-white px-4 py-1 bottom-75 min-w-100px text-center mx-auto left-1/2">
        <div v-html="title"></div>
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
