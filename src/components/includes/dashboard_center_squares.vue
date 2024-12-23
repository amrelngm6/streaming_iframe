<template>
    
        <div class="border-muted-200 dark:border-muted-700 dark:bg-muted-800 relative w-full border bg-white transition-all duration-300 rounded-md p-6" v-if="content">
            <div class="mb-10 pb-6">
                <h3 class="font-heading text-base font-semibold leading-tight text-muted-800 dark:text-white" v-text="$parent.translate('Some important Stats')"></h3>
                <p class="py-2 text-gray-400 mb-10" v-text="$parent.translate('some important stats about the performance')"></p>
            </div>
            <div class="grid gap-4 md:grid-cols-2 mt-10 pt-6">
                <div class="bg-gray-50  flex items-center gap-2 rounded-md p-4">
                    <div class="relative inline-flex shrink-0 items-center justify-center h-12 w-12 rounded-full bg-primary-100 text-primary-500 dark:bg-primary-500/20 dark:text-primary-400 dark:border-primary-500 dark:border-2"><svg data-v-cd102a71="" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="icon h-5 w-5" width="1em" height="1em" viewBox="0 0 256 256">
                            <g fill="currentColor">
                                <path d="m219.84 73.16l-88-48.16a8 8 0 0 0-7.68 0l-88 48.18a8 8 0 0 0-4.16 7v95.64a8 8 0 0 0 4.16 7l88 48.18a8 8 0 0 0 7.68 0l88-48.18a8 8 0 0 0 4.16-7V80.18a8 8 0 0 0-4.16-7.02ZM128 168a40 40 0 1 1 40-40a40 40 0 0 1-40 40Z" opacity=".2"></path>
                                <path d="M128 80a48 48 0 1 0 48 48a48.06 48.06 0 0 0-48-48Zm0 80a32 32 0 1 1 32-32a32 32 0 0 1-32 32Zm95.68-93.85l-88-48.15a15.88 15.88 0 0 0-15.36 0l-88 48.17a16 16 0 0 0-8.32 14v95.64a16 16 0 0 0 8.32 14l88 48.17a15.88 15.88 0 0 0 15.36 0l88-48.17a16 16 0 0 0 8.32-14V80.18a16 16 0 0 0-8.32-14.03ZM128 224l-88-48.18V80.18L128 32l88 48.17v95.63Z"></path>
                            </g>
                        </svg></div>
                    <div>
                        <h2 class="font-heading text-base font-semibold leading-tight text-muted-800 dark:text-white" v-text="currency.symbol+content.avg_sales"></h2>
                        <p class="font-alt text-sm font-normal leading-normal leading-normal"><span class="text-muted-500 dark:text-muted-400" v-text="$parent.translate('Sales average')"></span></p>
                    </div>
                </div>
                <div class="bg-gray-50  flex items-center gap-2 rounded-md p-4">
                    <div class="relative inline-flex shrink-0 items-center justify-center h-12 w-12 rounded-full bg-amber-100 text-amber-500 dark:border-2 dark:border-amber-500 dark:bg-amber-500/20 dark:text-amber-400"><svg data-v-cd102a71="" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="icon h-5 w-5" width="1em" height="1em" viewBox="0 0 256 256">
                            <g fill="currentColor">
                                <path d="m200 152l-40 40l-64-16l-56-40l32-64l56-16l56 16h-40l-45.66 44.29a8 8 0 0 0 1.38 12.42C117.23 139.9 141 139.13 160 120Z" opacity=".2"></path>
                                <path d="M119.76 217.94A8 8 0 0 1 112 224a8.13 8.13 0 0 1-2-.24l-32-8a8 8 0 0 1-2.5-1.11l-24-16a8 8 0 1 1 8.88-13.31l22.84 15.23l30.66 7.67a8 8 0 0 1 5.88 9.7Zm132.69-96.46a15.89 15.89 0 0 1-8 9.25l-23.68 11.84l-55.08 55.09a8 8 0 0 1-7.6 2.1l-64-16a8.06 8.06 0 0 1-2.71-1.25l-55.52-39.64l-24.28-12.14a16 16 0 0 1-7.16-21.46l24.85-49.69a16 16 0 0 1 21.46-7.16l22.06 11l53-15.14a8 8 0 0 1 4.4 0l53 15.14l22.06-11a16 16 0 0 1 21.46 7.16l24.85 49.69a15.9 15.9 0 0 1 .89 12.21Zm-46.18 12.94L179.06 80h-31.82L104 122c12.66 8.09 32.51 10.32 50.32-7.63a8 8 0 0 1 10.68-.61l34.41 27.57Zm-187.54-18l17.69 8.85l24.85-49.69l-17.69-8.85ZM188 152.66l-27.71-22.19c-19.54 16-44.35 18.11-64.91 5a16 16 0 0 1-2.72-24.82a.6.6 0 0 1 .08-.08l44.86-43.51l-9.6-2.74l-50.42 14.41l-27.37 54.73l49.2 35.15l58.14 14.53Zm49.24-36.24l-24.82-49.69l-17.69 8.85l24.85 49.69Z"></path>
                            </g>
                        </svg></div>
                    <div>
                        <h2 class="font-heading text-base font-semibold leading-tight text-muted-800 dark:text-white" v-text="currency.symbol+content.avg_bookings"></h2>
                        <p class="font-alt text-sm font-normal leading-normal leading-normal"><span class="text-muted-500 dark:text-muted-400" v-text="$parent.translate('Bookings average')"></span></p>
                    </div>
                </div>
                <div class="bg-gray-50  flex items-center gap-2 rounded-md p-4">
                    <div class="relative inline-flex shrink-0 items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-500 dark:border-2 dark:border-green-500 dark:bg-green-500/20 dark:text-green-400"><svg data-v-cd102a71="" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="icon h-5 w-5" width="1em" height="1em" viewBox="0 0 256 256">
                            <g fill="currentColor">
                                <path d="M240 104L128 224L80 104l48-64h56Z" opacity=".2"></path>
                                <path d="m246 98.73l-56-64a8 8 0 0 0-6-2.73H72a8 8 0 0 0-6 2.73l-56 64a8 8 0 0 0 .17 10.73l112 120a8 8 0 0 0 11.7 0l112-120a8 8 0 0 0 .13-10.73ZM222.37 96H180l-36-48h36.37ZM74.58 112l30.13 75.33L34.41 112Zm89.6 0L128 202.46L91.82 112ZM96 96l32-42.67L160 96Zm85.42 16h40.17l-70.3 75.33ZM75.63 48H112L76 96H33.63Z"></path>
                            </g>
                        </svg></div>
                    <div>
                        <h2 class="font-heading text-base font-semibold leading-tight text-muted-800 dark:text-white" v-text="content.avg_bookings_count"></h2>
                        <p class="font-alt text-sm font-normal leading-normal leading-normal"><span class="text-muted-500 dark:text-muted-400" v-text="$parent.translate('AVG_daily_bookings_count')"> </span></p>
                    </div>
                </div>
                <div class="bg-gray-50 flex items-center gap-2 rounded-md p-4">
                    <div class="relative inline-flex shrink-0 items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-500 dark:border-2 dark:border-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400"><svg data-v-cd102a71="" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="icon h-5 w-5" width="1em" height="1em" viewBox="0 0 256 256">
                            <g fill="currentColor">
                                <path d="M232 96H24l104-64Z" opacity=".2"></path>
                                <path d="M24 104h24v64H32a8 8 0 0 0 0 16h192a8 8 0 0 0 0-16h-16v-64h24a8 8 0 0 0 4.19-14.81l-104-64a8 8 0 0 0-8.38 0l-104 64A8 8 0 0 0 24 104Zm40 0h32v64H64Zm80 0v64h-32v-64Zm48 64h-32v-64h32ZM128 41.39L203.74 88H52.26ZM248 208a8 8 0 0 1-8 8H16a8 8 0 0 1 0-16h224a8 8 0 0 1 8 8Z"></path>
                            </g>
                        </svg></div>
                    <div>
                        <h2 class="font-heading text-base font-semibold leading-tight text-muted-800 dark:text-white" v-text="content.avg_products_count"></h2>
                        <p class="font-alt text-sm font-normal leading-normal leading-normal"><span class="text-muted-500 dark:text-muted-400" v-text="$parent.translate('AVG_daily_products_sales')"> </span></p>
                    </div>
                </div>
            </div>
        </div>
</template>
<script>

export default 
{
    props: [
        'content',
        'setting',
        'currency'
    ],

};
</script>
