<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">

            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
            <div class="card mb-9 shadow-sm">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                        <!--begin::Image-->
                        <div
                            class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                            <img class="mw-50px mw-lg-75px" :src="activeItem.picture" alt="image">
                        </div>
                        <!--end::Image-->

                        <!--begin::Wrapper-->
                        <div class="flex-grow-1 overflow-hidden">
                            <!--begin::Head-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <!--begin::Details-->
                                <div class="d-flex flex-column">
                                    <!--begin::Status-->
                                    <div class="d-flex align-items-center mb-1">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3"
                                            v-text="activeItem.name"></a>
                                        <span class="badge badge-light-success me-auto"
                                            v-text="translate(activeItem.type)"></span>
                                    </div>
                                    <!--end::Status-->

                                    <!--begin::Description-->
                                    <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500 truncate"
                                        v-text="activeItem.description"></div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Head-->

                            <!--begin::Info-->
                            <div class="d-flex flex-wrap justify-content-start">
                                <!--begin::Stats-->
                                <div class="d-flex flex-wrap">
                                    <!--begin::Stat-->
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <div class="fs-4 fw-bold"
                                                v-text="formatCustomTime(activeItem.created_at, 'MMMM DD, Y - HH:mm a')">
                                            </div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="fw-semibold fs-6 text-gray-500" v-text="translate('Upload Date')">
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->

                                    <!--begin::Stat-->
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-arrow-down fs-3 text-danger me-2"><span
                                                    class="path1"></span><span class="path2"></span></i>
                                            <div class="fs-4 fw-bold counted" v-text="activeItem.views_count"></div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="fw-semibold fs-6 text-gray-500" v-text="translate('Views')"></div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->

                                    <!--begin::Stat-->
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-arrow-up fs-3 text-success me-2"><span
                                                    class="path1"></span><span class="path2"></span></i>
                                            <div class="fs-4 fw-bold counted" v-text="activeItem.comments_count"></div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="fw-semibold fs-6 text-gray-500" v-text="translate('Comments')">
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->
                                </div>
                                <!--end::Stats-->

                                <!--begin::Users-->
                                <div class="symbol-group symbol-hover mb-3">
                                    <!--begin::User-->
                                    <div v-for="comment in activeItem.comments"  @mouseover="comment.showTip = true" @mouseleave="comment.showTip = false" class="symbol symbol-35px symbol-circle" >
                                        <img alt="Pic" v-if="comment && comment.customer" :src="comment.customer.picture">
                                        <span v-if="comment && activeItem.customer && !activeItem.customer.picture" v-text="activeItem.name.substring(0, 1)"
                                            class="symbol-label bg-info text-inverse-info fw-bold"></span>
                                        <tooltip v-if="comment.showTip && comment.customer" :title="comment.customer.name" ></tooltip>
                                    </div>

                                    <!--begin::All users-->
                                    <a href="#" class="symbol symbol-35px symbol-circle">
                                        <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                            v-text="activeItem.comments_count > 10 ? (activeItem.comments_count - 10) : '+'"></span>
                                    </a>
                                    <!--end::All users-->
                                </div>
                                <!--end::Users-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Details-->

                    <div class="separator"></div>

                    <!--begin::Nav-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                        <!--begin::Nav item-->
                        <li class="nav-item" v-for="tab in tabs">
                            <a class="nav-link text-active-primary py-5 me-6" @click="activeTab = tab"
                                :class="tab == activeTab ? 'active' : ''" href="javascript:;" v-text="tab"></a>
                        </li>
                        <!--end::Nav item-->
                    </ul>
                    <!--end::Nav-->
                </div>
            </div>


            <div v-if="activeTab == translate('Info')">

                <div class=" card w-full py-10 shadow-sm">
                    <div class="w-full stepper stepper-links ">
                        <div class="card-header cursor-pointer">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0" v-text="translate('Media info')"></h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <div class="card-body pt-0">
                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Link')"></label>
                                <div class="col-lg-8"><a target="_blank"
                                        :href="'/' + activeItem.type + '/' + activeItem.media_id"
                                        class="fw-bold fs-6 text-gray-800"
                                        v-text="conf.url + activeItem.type + '/' + activeItem.media_id"></a></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Media Name')"></label>
                                <div class="col-lg-8"><span class="fw-bold fs-6 text-gray-800"
                                        v-text="activeItem.name"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted"
                                    v-text="translate('Description')"></label>
                                <div class="col-lg-8 fv-row"><span class="fw-semibold text-gray-800 fs-6"
                                        v-text="activeItem.description"></span></div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Duration')"></label>
                                <div class="col-lg-8 fv-row"><span class="fw-semibold text-gray-800 fs-6"
                                        v-text="activeItem.field.duration ? toHHMMSS(activeItem.field.duration) : '0'"></span>
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Filesize')"></label>
                                <div class="col-lg-8 fv-row"><span class="fw-semibold text-gray-800 fs-6"
                                        v-text="activeItem.field.filesize ?? '0'"></span></div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fw-semibold text-muted" v-text="translate('Bitrate')"></label>
                                <div class="col-lg-8 fv-row"><span class="fw-semibold text-gray-800 fs-6"
                                        v-text="activeItem.field.bitrate ?? '0'"></span></div>
                            </div>
                            <!--end::Input group-->

                        </div>
                    </div>
                </div>

                <div class="card shadow-sm ">

                    <!--begin::Card header-->
                    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                        data-bs-target="#kt_account_deactivate" aria-expanded="true"
                        aria-controls="kt_account_deactivate">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0" v-text="translate('Delete this item')"></h3>
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Content-->
                    <div id="" class="">
                        <!--begin::Form-->
                        <form id="kt_account_deactivate_form" class="form fv-plugins-bootstrap5 fv-plugins-framework"
                            novalidate="novalidate">

                            <!--begin::Card body-->
                            <div class="card-body border-top p-9">

                                <!--begin::Notice-->
                                <div
                                    class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                                    <!--begin::Icon-->
                                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                                    <!--end::Icon-->

                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1 ">
                                        <!--begin::Content-->
                                        <div class=" fw-semibold">
                                            <h4 class="text-gray-900 fw-bold" v-text="translate('Delete this media')">
                                            </h4>

                                            <div class="fs-6 text-gray-700 "> <span
                                                    v-text="translate('Remove this media and its related files and all related information')"></span>
                                            </div>
                                        </div>
                                        <!--end::Content-->

                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Notice-->

                                <!--begin::Form input row-->
                                <div class="form-check form-check-solid fv-row fv-plugins-icon-container">
                                    <input name="deactivate" class="form-check-input" type="checkbox" value="" required
                                        id="deactivate">
                                    <label class="form-check-label fw-semibold ps-2 fs-6" for="deactivate"
                                        v-text="translate('I Confirm to delete this media')"></label>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <!--end::Form input row-->
                            </div>
                            <!--end::Card body-->

                            <!--begin::Card footer-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a id="kt_account_deactivate_account_submit" @click="removeMedia"
                                    class="btn btn-danger fw-semibold" v-text="translate('Delete')"></a>
                            </div>
                            <!--end::Card footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Content-->
                </div>

            </div>

            <div class=" card w-full py-10" v-if="activeTab == translate('Comments')">
                <div class="w-full stepper stepper-links ">
                    <div class="card-header cursor-pointer">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0" v-text="translate('Latest Comments')"></h3>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <div class="w-full">
                        <div class="card-body pt-0">
                            <div class="card-body p-9">

                                <div class="timeline timeline-border-dashed">
                                    <div class="timeline-item" v-for="comment in activeItem.comments">
                                        <div class="timeline-line"></div>
                                        <div class="timeline-icon me-4">
                                            <img class="w-12 h-12 rounded-full" :src="comment.customer.picture ?? ''"
                                                alt="img">
                                        </div>
                                        <div class="timeline-content mb-10 mt-n2">
                                            <!--begin::Timeline heading-->
                                            <div class="overflow-auto pe-3">
                                                <!--begin::Title-->
                                                <div class="fs-5 fw-semibold mb-2" v-text="comment.comment"></div>
                                                <!--end::Title-->

                                                <!--begin::Description-->
                                                <div class="d-flex align-items-center mt-1 fs-6 gap-2 ">
                                                    <div class="symbol symbol-circle symbol-25px">
                                                        <vue-feather class="w-5 pt-2"
                                                            type="message-square p-1"></vue-feather>
                                                    </div>
                                                    <div class="text-muted me-2 fs-7 fw-bold"
                                                        v-text="comment.customer.name ?? ''"></div>
                                                    |
                                                    <div class="text-muted me-2 fs-7"
                                                        v-text="formatCustomTime(comment.created_at, 'MMMM DD, Y - HH:mm a')">
                                                    </div>
                                                </div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Timeline heading-->
                                        </div>
                                        <!--end::Timeline content-->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card w-full p-10" v-if="activeTab == translate('Files')">
                <!-- Timeline -->
                <div>
                    <!-- Item -->
                    <div class="group relative flex gap-x-5" v-for="file in activeItem.files">
                        <!-- Icon -->
                        <div
                            class="relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                            <div class="relative z-10 size-6 flex justify-center items-center">
                                <vue-feather type="video" v-if="activeItem.type == 'video'" class="shrink-0 size-6 text-gray-600 dark:text-neutral-400" />
                                <vue-feather type="music" v-if="activeItem.type == 'audio'" class="shrink-0 size-6 text-gray-600 dark:text-neutral-400" />
                                <vue-feather type="headphones" v-if="activeItem.type == 'audiobook'" class="shrink-0 size-6 text-gray-600 dark:text-neutral-400" />
                            </div>
                        </div>
                        <!-- End Icon -->

                        <!-- Right Content -->
                        <div class="grow pb-8 text-lg group-last:pb-0">
                            <h3 class="mb-1 text-xs text-gray-600 dark:text-neutral-400" v-text="translate(file.type)"></h3>

                            <a :href="file.path" class="font-semibold text-xl py-1 text-gray-800 dark:text-neutral-200"  v-text="file.title.length ? file.title : activeItem.name"></a>

                            <p class="mt-1  text-gray-600 dark:text-neutral-400">
                            </p>

                            <ul class="px-0 mt-3  flex gap-10">
                                <li class="ps-1  text-gray-600 flex gap-2 dark:text-neutral-400">
                                    <span v-text="translate('Storage location')"></span>
                                    <b v-text="file.storage"></b>
                                </li>
                                <li class="ps-1  text-gray-600 flex gap-2 dark:text-neutral-400">
                                    <span v-text="translate('File size')"></span>
                                    <b v-text="Math.round((file.field.filesize ?? activeItem.field.filesize) / 1000000, 2) + translate('MB')"></b>
                                </li>
                                <li class="ps-1  text-gray-600 flex gap-2 dark:text-neutral-400">
                                    <span v-text="translate('Duration')"></span>
                                    <b v-text="toHHMMSS(file.field.duration ?? activeItem.field.duration)"></b>
                                </li>
                                <li class="ps-1 flex gap-2 text-gray-600 dark:text-neutral-400">
                                    <span v-text="translate('Bitrate')"></span>
                                    <b v-text="Math.round(file.field.bitrate ?? activeItem.field.bitrate, 2)"></b>
                                </li>
                            </ul>
                        </div>
                        <!-- End Right Content -->
                    </div>
                    <!-- End Item -->

                </div>
                <!-- End Timeline -->
            </div>
        </div>
    </div>
</template>
<script>

import close_icon from '@/components/svgs/Close.vue';
import delete_icon from '@/components/svgs/trash.vue';
import route_icon from '@/components/svgs/route.vue';
import car_icon from '@/components/svgs/car.vue';

import 'vue3-easy-data-table/dist/style.css';
import Vue3EasyDataTable from 'vue3-easy-data-table';

import { defineAsyncComponent, ref } from 'vue';
import { translate, getProgressWidth,  formatCustomTime, toHHMMSS, deleteByKey } from '@/utils.vue';

const form_field = defineAsyncComponent(() =>
    import('@/components/includes/form_field.vue')
);

import field from '@/components/includes/Field.vue';
import tooltip from '@/components/includes/tooltip.vue';

export default
    {
        components: {
            'vue-medialibrary-field': field,
            'datatabble': Vue3EasyDataTable,
            close_icon,
            tooltip,
            delete_icon,
            car_icon,
            route_icon,
            form_field,
        },
        name: 'Translations',
        emits: ['callback'],
        setup(props, { emit }) {

            const showEditSide = ref(false);
            const fields = ref([]);
            const activeItem = ref({ translations: props.languages });
            const activeTab = ref('Info');
            const content = ref({});
            const fillable = ref(['Info']);

            if (props.item) {
                activeItem.value = props.item
                fields.value = props.item.translation ?? []
            }

            const tabs = ref([translate('Info'), translate('Files'), translate('Comments')]);

            const back = () => {
                emit('callback');
            }

            const removeMedia = () => {
                return deleteByKey('media_id', activeItem.value, 'MediaItem.delete')
            }

            const progressWidth = () => {
                let requiredData = ['name', 'description', 'double_cost_month', 'double_cost_quarter', 'double_cost_year', 'status'];

                return getProgressWidth(requiredData, activeItem);
            }

            const switchField = (field, key) => {
                activeField.value = key;
                showModal.value = true
            }
            const addField = () => {
                if (!activeItem.value.fields) {
                    activeItem.value.fields = [{ 'title': '' }];
                } else {
                    activeItem.value.fields.push({ 'title': '' })
                }

                activeField.value = activeItem.value.fields.length - 1;
                showModal.value = true
            }



            const activeField = ref(0);
            const showModal = ref(false);

            return {
                removeMedia,
                tabs,
                fields,
                showModal,
                switchField,
                addField,
                toHHMMSS,
                activeField,
                progressWidth,
                showEditSide,
                content,
                fillable,
                activeItem,
                activeTab,
                translate,
                formatCustomTime,
                back
            };
        },

        props: [
            'conf',
            'path',
            'system_setting',
            'setting',
            'item',
            'languages',
        ],

    };
</script>