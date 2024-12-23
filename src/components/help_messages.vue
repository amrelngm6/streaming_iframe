<template>
    <div class=" w-full">
        <help_message_details :item="activeItem" v-if="showEditSide" ref="activeHelpMessage" @callback="closeMessage" />
        <div class="container-fluid" v-if="!showEditSide">

            <div class="card">
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-xl-row p-7">
                        <!--begin::Content-->
                        <div class="flex-lg-row-fluid me-xl-15 mb-20 mb-xl-0">



                            <!--begin::Tickets-->
                            <div class="mb-0">
                                <!--begin::Search form-->
                                <form method="post" action="#" class="form mb-15">
                                    <!--begin::Input wrapper-->
                                    <div class="position-relative">
                                        <i
                                            class="ki-duotone ki-magnifier fs-1 text-primary position-absolute top-50 translate-middle ms-9"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <input type="text" class="form-control form-control-lg form-control-solid ps-14"
                                            name="search" value="" placeholder="Search">
                                    </div>
                                    <!--end::Input wrapper-->
                                </form>
                                <!--end::Search form-->

                                <!--begin::Heading-->
                                <h1 class="text-gray-900 mb-10" v-text="content.title"></h1>
                                <!--end::Heading-->

                                <!--begin::Tickets List-->
                                <div class="w-full " v-for="ticket in content.items">
                                    <!--begin::Ticket-->
                                    <div class="d-flex mb-10 gap-4" v-if="ticket.status == activeStatus" >
                                        <!--begin::Symbol-->
                                        <vue-feather type="bell" class="mt-5"></vue-feather>

                                        <div class="d-flex flex-column " @click="activeItem = ticket, showEditSide = true" >
                                            <!--begin::Content-->
                                            <div class="d-flex align-items-center mb-2">
                                                <a  href="javascript:;" class="font-semibold text-gray-800 text-hover-primary fs-4 me-3 " v-text="ticket.title"></a>
                                                <span class="badge badge-light my-1 bg-gray-100" v-text="ticket.subject"></span>
                                                <span class="badge badge-light my-1 text-white" :class="{'bg-primary': ticket.status == 'new', 'bg-danger':ticket.status == 'completed'}" v-text="ticket.status"></span>
                                            </div>
                                            <span class="text-muted fw-semibold fs-6" v-text="ticket.message"></span>
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Ticket-->
                                </div>
                                <!--end::Tickets List-->
                            </div>
                            <!--end::Tickets-->
                        </div>
                        <!--end::Content-->
                        <!--begin::Sidebar-->
                        <div class="flex-column flex-lg-row-auto w-100 mw-lg-300px mw-xxl-350px">

                            <!--begin::More channels-->
                            <div class="card-rounded bg-primary bg-opacity-5 p-10 mb-15">
                                <!--begin::Title-->
                                <h2 class="text-gray-900 fw-bold mb-11" v-text="translate('Status')"></h2>

                                <div class="d-flex align-items-center mb-10 gap-4" v-for="statusItem in statusList">
                                    <!--begin::Icon-->
                                    <vue-feather :type="statusItem.icon"></vue-feather>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="d-flex flex-column cursor-pointer "
                                        @click="switchStatus(statusItem.status)" >
                                        <h5 class="" :class="statusItem.status == activeStatus ? 'text-gray-800' : 'text-gray-500'" v-text="statusItem.text"></h5>

                                        <!--begin::Section-->
                                        <div class="fw-semibold">
                                            <!--begin::Desc-->
                                            <span class="text-muted" v-text="statusItem.desc"></span>
                                            <!--end::Desc-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Item-->

                            </div>
                            <!--end::More channels-->
                        </div>
                        <!--end::Sidebar-->
                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Card body-->
            </div>
            
        </div>
    </div>
</template>
<script>

import help_message_details from '@/components/help_message_details.vue';


import { defineAsyncComponent, ref } from 'vue';
import { translate, handleGetRequest, handleRequest, deleteByKey, showAlert } from '@/utils.vue';

export default
    {
        components: {
            help_message_details,
            translate
        },

        setup(props) {

            const url = props.conf.url + props.path + '?load=json';

            const content = ref({});
            const activeItem = ref({});
            const showAddSide = ref(false);
            const showEditSide = ref(false);
            const activeStatus = ref('new');


            const closeMessage = () => {
                showEditSide.value = false;
                activeItem.value = null;
            }

            const switchStatus = (option) => {
                activeStatus.value = option
            }

            const showDetails = (item) => {
                showEditSide.value = true;
                activeItem.value = item;
            }


            /**
             * Handle actions from datatable buttons
             * Called From 'dataTableActions' component
             * 
             * @param String actionName 
             * @param Object data
             */
            const handleAction = (actionName, data) => {
                switch (actionName) {
                    case 'view':
                        // window.open(conf.url+data.content.prefix)
                        break;

                    case 'edit':
                        activeItem.value = data
                        break;

                    case 'delete':
                        deleteByKey(props.object_key, data, props.object_name + '.delete');
                        break;
                }
            }

            const load = () => {
                handleGetRequest(url).then(response => {
                    content.value = JSON.parse(JSON.stringify(response));
                });
            }

            load();

            let statusList = [{ text: translate('New'), status: 'new', icon: 'check', desc: translate('New tickets those not opened yet') }, { text: translate('Active'), status: 'active', icon: 'check-square', desc: translate('Tickets needs customer reply') }, { text: translate('Completed'), status: 'completed', icon: 'check-circle', desc: translate('Completed tickets') }];

            return {
                content,
                activeItem,
                closeMessage,
                switchStatus,
                showDetails,
                showAddSide,
                showEditSide,
                activeStatus,
                statusList,
                translate
            }
        },
        props: [
            'path',
            'langs',
            'setting',
            'conf',
            'auth',
        ],
    };
</script>