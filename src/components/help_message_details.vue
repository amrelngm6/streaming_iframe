<template>
    <div class=" w-full">

        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-n4 mx-n4 card-border-effect-none mb-n5 border-bottom-0 border-start-0 rounded-0">
                        <div class="card-body pb-4 mb-0">
                            <div class="row">
                                <div class="col-md">
                                    <div class="flex align-items-center">
                                        <div class="px-1 pt-1">
                                            <img v-if="item" :src="item.user ? (item.user.photo ? item.user.photo : item.user.picture) : ''" alt="" width="36" height="36" class="rounded">
                                        </div>
                                        <!--end col-->
                                        <div class="col-md">
                                            <h4 class="fw-semibold px-1" id="ticket-title" v-text="item.user ? item.user.name : ''"></h4>
                                            <div class="hstack gap-3 flex h-8" >
                                                <div class="text-muted"><i
                                                        class="ri-building-line align-bottom me-1"></i><span
                                                        id="ticket-client" v-text="item.subject"></span></div>
                                                <div class="vr"></div>
                                                <div class="text-muted"><span v-text="translate('Created at')"></span> : <span
                                                        class="fw-medium " id="create-date" v-text="item.date"></span></div>
                                                <div class="vr"></div>
                                                <div class="text-muted"><span v-text="translate('Last update')"></span> : <span
                                                        class="fw-medium " id="update-date"
                                                        v-text="item.last_update"></span></div>
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill fs-12 text-white" id="ticket-status"
                                                    v-if="item.status" v-text="item.status" :class="item.status == 'new' ? 'bg-info' : 'bg-success'"></div>
                                                <div class="badge rounded-pill bg-danger fs-12 text-white" id="ticket-priority"
                                                    v-if="item.priority" v-text="item.priority"></div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <span class="w-auto py-2 px-4 cursor-pointer text-lg" @click="emit('callback')"><vue-feather class="w-5" type="x-circle"></vue-feather></span>
                                    </div>
                                    <!--end row-->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div><!-- end card -->
                </div><!-- end col -->
            </div><!-- end row -->

            <div class="lg:flex gap-6 mt-4">
                <div class="col-xxl-9 w-full">
                    <div class="card">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-uppercase mb-3" v-text="translate('Ticket Discripation')"></h6>
                            <p class="text-muted font-bold pt-10" v-text="item.message"></p>
                        </div>
                        <!--end card-body-->
                        <div class="card-body p-4">
                            <h5 class="card-title text-sm font-semibold mb-4" v-text="translate('Comments')"></h5>

                            <div data-simplebar="init" style="max-height: 300px;" class="overflow-y-auto">
                                <div class="simplebar-wrapper" >
                                    <div class="simplebar-height-auto-observer-wrapper">
                                        <div class="simplebar-height-auto-observer"></div>
                                    </div>
                                    <div class="simplebar-mask">
                                        <div class="simplebar-offset" >
                                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                                aria-label="scrollable content">
                                                <div class="simplebar-content" >
                                                    <div class="flex gap-2 mb-4" v-for="comment in item.comments">
                                                        <div class="flex-shrink-0" v-if="comment.user">
                                                            <img :src="item.user.photo ? item.user.photo : item.user.picture" alt=""
                                                                class="h-10 w-10 mt-2 avatar-xs rounded-circle">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3" v-if="comment.user">
                                                            <h5 class="fs-13 flex gap-2"><span v-text="comment.user.name"></span>
                                                                <small class="text-muted" v-text="comment.date"></small>
                                                            </h5>
                                                            <p class="text-muted" v-text="comment.comment"></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form action="/api/create" method="POST" data-refresh="1" id="add-device-form" class="action my-2 rounded-lg  pb-2">
                                <input name="type" type="hidden" value="HelpMessageComment.create">
                                <input name="params[message_id]" type="hidden" :value="item.message_id">
                                
                                <input v-if="model_id && column && column.column_type == 'hidden'" :name="'params['+column.key+']'" type="hidden" v-model="column.default">
                                
                                <div class="row g-3">
                                    <div class="col-lg-12">
                                        <label for="exampleFormControlTextarea1" class="form-label"
                                            v-text="translate('WRITE_COMMENT')"></label>
                                        <textarea name="params[comment]" class="form-control bg-light border-light"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder="Enter comments"></textarea>
                                    </div>
                                    <div class="col-lg-12 text-end mt-4">
                                        <button type="submit" href="javascript:void(0);" class="btn btn-primary" v-text="translate('Send')"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
                <div class="w-96">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0" v-text="translate('Ticket Details')"></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless align-middle mb-0 w-full">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('Ticket')"></td>
                                            <td class="py-2" >#<span id="t-no" v-text="item.message_id"></span> </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('User')"></td>
                                            <td id="t-client" class="py-2" v-text="item.user ? item.user.name : ''"></td>
                                        </tr>

                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('Status')"></td>
                                            <td class="py-2" >
                                                <span class="font-bold" id="t-status" v-text="item.status"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('Priority')"></td>
                                            <td class="py-2" >
                                                <span class="badge bg-danger text-white" id="t-priority" v-text="item.priority"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium py-2" v-text="translate('Created at')"></td>
                                            <td id="c-date" class="py-2" v-text="item.date"></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('Last update')"></td>
                                            <td id="d-date" class="py-2" v-text="item.last_update"></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium py-2 " v-text="translate('Close ticket')"></td>
                                            <td id="d-date" class="py-2 flex" >
                                                <div @click="close" class="cursor-pointer hover:bg-red-800 hover:text-gray-100 px-3 py-2 text-sm border-red-600 border-1 rounded border mt-2 block text-center" >
                                                    <vue-feather class="w-4 mx-1" type="power"></vue-feather>
                                                    <span @click="close"  v-text="translate('Close')"></span>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div>
</div>
</template>
<script>
import {translate, handleGetRequest, handleRequest, showAlert} from '@/utils.vue';

export default
{
    components: {
        translate
    },
    emits: ['callback'],
    setup(props, {emit}) {
        
        const close = () =>  {
            
            if (!window.confirm(translate('confirm_close_ticket')))
            {
                return null;
            }

            var params = new URLSearchParams();
            params.append('type', 'HelpMessage.close')
            params.append('params[message_id]', props.item.message_id)
            params.append('params[status]', 'completed')
            handleRequest(params, '/api/update').then(response => {
                showAlert(response.result)
            })
        }

        return  {
            translate,
            emit,
            close
        }
    },
    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
        'item'
    ],
};
</script>