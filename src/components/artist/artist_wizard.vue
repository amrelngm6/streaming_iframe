<template>
    <div class="w-full flex overflow-auto">
        <div class=" w-full relative">

            <close_icon class="absolute top-4 right-4 z-10 cursor-pointer" @click="back" />
            <div class="card mb-9 shadow-md">
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
                                        <a href="#" class="text-gray-800 text-hover-primary fs-2 font-semibold me-3"
                                            v-text="activeItem.name"></a>
                                        <span class="badge badge-light-success me-auto"
                                            v-text="translate('Artist')"></span>
                                    </div>
                                    <!--end::Status-->

                                    <div class="d-flex flex-wrap font-semibold fs-4 mb-4 pe-2">
                                        <a href="#"
                                            class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                            <vue-feather class="w-5 mx-1" type="smartphone"></vue-feather>
                                            <span v-text="activeItem.field ? activeItem.field.phone : ''"></span>
                                        </a>
                                        <a href="#"
                                            class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                            <vue-feather class="w-5 mx-1" type="at-sign"></vue-feather>
                                            <span v-text="activeItem.email"></span>
                                        </a>
                                    </div>

                                    <!--begin::Description-->
                                    <div class="d-flex flex-wrap font-semibold mb-4 fs-5 text-gray-500 truncate"
                                        v-text="activeItem.field.about ?? ''"></div>
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
                                            <div class="fs-4 font-semibold" v-if="activeItem.subscription"
                                                v-text="formatCustomTime(activeItem.subscription.end_date, 'MMMM DD, Y')">
                                            </div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="font-semibold fs-4 text-gray-500"
                                            v-text="translate('Subscription Due Date')">
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->

                                    <!--begin::Stat-->
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center" v-if="activeItem.subscription">
                                            <div class="fs-4 font-semibold counted"
                                                v-text="activeItem.subscription.package ? activeItem.subscription.package.name : ''">
                                            </div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="font-semibold fs-4 text-gray-500" v-text="translate('Package')"></div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->

                                    <!--begin::Stat-->
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <!--begin::Number-->
                                        <div class="d-flex align-items-center">
                                            <div class="fs-4 font-semibold counted"
                                                v-text="activeItem.media_views_sum_times">
                                            </div>
                                        </div>
                                        <!--end::Number-->

                                        <!--begin::Label-->
                                        <div class="font-semibold fs-4 text-gray-500" v-text="translate('Media Views')">
                                        </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Stat-->
                                </div>
                                <!--end::Stats-->


                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Details-->

                    <div class="separator"></div>

                    <!--begin::Nav-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 font-semibold">
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

                <div class=" card w-full py-10 shadow-md">
                    <div class="w-full stepper stepper-links ">
                        <div class="card-header cursor-pointer">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="font-semibold m-0" v-text="translate('Customer info')"></h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <div class="card-body pt-0">
                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('Profile')"></label>
                                <div class="col-lg-8"><a target="_blank" :href="'/artist/' + activeItem.customer_id"
                                        class="font-semibold fs-3 text-gray-800"
                                        v-text="conf.url + '/artist/' + activeItem.customer_id"></a></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted"
                                    v-text="translate('Customer Name')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.name"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('Email')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.email"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('Phone')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.field.phone ?? ''"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('City')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.field.city ?? ''"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('Country')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.field.country ?? ''"></span></div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <label class="col-lg-4 fs-4  font-semibold text-muted" v-text="translate('website')"></label>
                                <div class="col-lg-8"><span class="font-semibold fs-3 text-gray-800"
                                        v-text="activeItem.field.website ?? ''"></span></div>
                            </div>
                            <!--end::Row-->

                        </div>
                    </div>
                </div>

                <div class="card shadow-md ">

                    <!--begin::Card header-->
                    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                        data-bs-target="#kt_account_deactivate" aria-expanded="true"
                        aria-controls="kt_account_deactivate">
                        <div class="card-title m-0">
                            <h3 class="font-semibold m-0" v-text="translate('Delete this item')"></h3>
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
                                        <div class=" font-semibold">
                                            <h4 class="text-gray-900 font-semibold"
                                                v-text="translate('Deactivate this customer')">
                                            </h4>

                                            <div class="fs-3 text-gray-700 "> <span
                                                    v-text="translate('Deactivate this customer and disable its all related items')"></span>
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
                                    <label class="form-check-label font-semibold ps-2 fs-3" for="deactivate"
                                        v-text="translate('I Confirm to delete this customer')"></label>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <!--end::Form input row-->
                            </div>
                            <!--end::Card body-->

                            <!--begin::Card footer-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a id="kt_account_deactivate_account_submit" @click="removeCustomer"
                                    class="btn btn-danger font-semibold" v-text="translate('Deactivate')"></a>
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
                            <h3 class="font-semibold m-0" v-text="translate('Latest Comments')"></h3>
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
                                            <img class="w-12 h-12 rounded-full" :src="activeItem.picture ?? ''"
                                                alt="img">
                                        </div>
                                        <div class="timeline-content mb-10 mt-n2">
                                            <!--begin::Timeline heading-->
                                            <div class="overflow-auto pe-3">
                                                <!--begin::Title-->
                                                <div class="fs-5 font-semibold mb-2" v-text="comment.comment"></div>
                                                <!--end::Title-->

                                                <!--begin::Description-->
                                                <div class="d-flex align-items-center mt-1 fs-3 gap-2 ">
                                                    <div class="symbol symbol-circle symbol-25px">
                                                        <vue-feather class="w-5 pt-2"
                                                            type="message-square p-1"></vue-feather>
                                                    </div>
                                                    <div class="text-muted me-2 fs-7 font-semibold"
                                                        v-text="activeItem.name ?? ''"></div>
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

            <div class="card w-full p-10" v-if="activeTab == translate('Stats')">
                <!-- Timeline -->
                <div>

                    <!-- Card Section -->
                    <div class="py-10 lg:py-14 mx-auto">
                        <!-- Grid -->
                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                            <!-- Card -->
                            <div
                                class="flex flex-col bg-white border shadow-md rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
                                <div class="p-4 md:p-5 flex gap-x-4">
                                    <div
                                        class="shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-neutral-800">
                                        <vue-feather type="music" class="shrink-0 size-5 text-gray-600 dark:text-neutral-400" />
                                    </div>

                                    <div class="grow">
                                        <div class="flex items-center gap-x-2">
                                            <p
                                                class="mb-0 uppercase tracking-wide text-gray-500 dark:text-neutral-500" v-text="translate('Uploaded Audio')">
                                            </p>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2">
                                            <h3
                                                class="text-xl sm:text-2xl text-gray-800 dark:text-neutral-200" v-text="activeItem.audio_items_count">
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Card -->

                            <!-- Card -->
                            <div
                                class="flex flex-col bg-white border shadow-md rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
                                <div class="p-4 md:p-5 flex gap-x-4">
                                    <div
                                        class="shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-neutral-800">
                                        <vue-feather type="book-open" class="shrink-0 size-5 text-gray-600 dark:text-neutral-400" />
                                    </div>

                                    <div class="grow">
                                        <div class="flex items-center gap-x-2">
                                            <p
                                                class="mb-0 uppercase tracking-wide text-gray-500 dark:text-neutral-500"  v-text="translate('Uploaded Audiobooks')">
                                            </p>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2">
                                            <h3 class="text-xl text-gray-800 dark:text-neutral-200" v-text="activeItem.audiobooks_count">
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Card -->

                            <!-- Card -->
                            <div
                                class="flex flex-col bg-white border shadow-md rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
                                <div class="p-4 md:p-5 flex gap-x-4">
                                    <div
                                        class="shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-neutral-800">
                                        <vue-feather type="video" class="shrink-0 size-5 text-gray-600 dark:text-neutral-400" />
                                    </div>

                                    <div class="grow">
                                        <div class="flex items-center gap-x-2">
                                            <p
                                                class="mb-0 uppercase tracking-wide text-gray-500 dark:text-neutral-500" v-text="translate('Uploaded Videos')">
                                            </p>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2">
                                            <h3
                                                class="text-xl sm:text-2xl text-gray-800 dark:text-neutral-200" v-text="activeItem.videos_count">
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Card -->

                            <!-- Card -->
                            <div
                                class="flex flex-col bg-white border shadow-md rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
                                <div class="p-4 md:p-5 flex gap-x-4">
                                    <div
                                        class="shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-neutral-800">
                                        <vue-feather type="video-off" class="shrink-0 size-5 text-gray-600 dark:text-neutral-400" />
                                    </div>

                                    <div class="grow">
                                        <div class="flex items-center gap-x-2">
                                            <p
                                                class="mb-0 uppercase tracking-wide text-gray-500 dark:text-neutral-500" v-text="translate('Short Videos')">
                                            </p>
                                        </div>
                                        <div class="mt-1 flex items-center gap-x-2">
                                            <h3 class="text-xl text-gray-800 dark:text-neutral-200" v-text="activeItem.short_videos_count">
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Card -->
                        </div>
                        <!-- End Grid -->
                    </div>
                    <!-- End Card Section -->


                    <div class="row">

                        <div class="col">
                            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                                <span class="fs-4 font-semibold text-info pb-1 px-2"
                                    v-text="translate('Created stations')"></span>
                                <span class="fs-lg-2tx font-semibold d-flex justify-content-center">
                                    <span data-kt-countup="true" data-kt-countup-value="63,240.00" class="counted"
                                        data-kt-initialized="1" v-text="activeItem.stations_count"></span>
                                </span>
                            </div>
                        </div>



                        <div class="col">
                            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                                <span class="fs-4 font-semibold text-success pb-1 px-2"
                                    v-text="translate('Created Channels')"></span>
                                <span class="fs-lg-2tx font-semibold d-flex justify-content-center">
                                    <span class="counted" v-text="activeItem.channels_count"></span>
                                </span>
                            </div>
                        </div>



                        <div class="col">
                            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                                <span class="fs-4 font-semibold text-danger pb-1 px-2"
                                    v-text="translate('Created Playlists')"></span>
                                <span class="fs-lg-2tx font-semibold d-flex justify-content-center">
                                    <span class="counted" v-text="activeItem.playlists_count"></span>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- End Timeline -->
            </div>
            <div class="mb-5 mb-xl-10" v-if="activeTab == translate('Subscription')">
                <div class="card  mb-5 mb-xl-10">
                    <!--begin::Card body-->
                    <div class="card-body">

                        <!--begin::Row-->
                        <div class="row" v-if="activeItem.subscription">
                            <!--begin::Col-->
                            <div class="col-lg-7">
                                <!--begin::Heading-->
                                <h3 class="mb-2"> <span v-text="translate('Active until')"></span> <span
                                        class="text-gray-800 font-semibold "
                                        v-text="formatCustomTime(activeItem.subscription.end_date ?? '', 'MMMM DD, Y')"></span>
                                </h3>
                                <p class="fs-3 text-gray-600 font-semibold mb-6 mb-lg-15"
                                    v-text="translate('Estimated date for this subscription')"></p>
                                <!--end::Heading-->

                                <!--begin::Info-->
                                <div class="fs-5 mb-2">
                                    <span class="text-gray-800 font-semibold me-1"> $<span class="text-gray-800 font-semibold "
                                            v-text="activeItem.subscription.total_cost"></span></span>
                                    <span class="text-gray-600 font-semibold"> <span
                                            v-text="translate('Per ' + activeItem.subscription.duration)"></span>
                                    </span>
                                </div>
                                <!--end::Info-->

                                <!--begin::Notice-->
                                <div class="fs-3 text-gray-600 font-semibold"><span
                                        v-text="translate('This subscription payment every')"></span> <span
                                        class="text-gray-800 font-semibold "
                                        v-text="translate(activeItem.subscription.duration)"></span> </div>
                                <!--end::Notice-->
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-lg-5">
                                <!--begin::Heading-->
                                <div class="d-flex text-muted font-semibold fs-5 mb-3">
                                    <span class="flex-grow-1 text-gray-800" v-text="translate('Dates')"></span>
                                </div>
                                <!--end::Heading-->

                                <!--begin::Progress-->
                                <div class="progress h-8px bg-light-primary mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        :style="{ width: getPercentageBetweenDates(activeItem.subscription.end_date, activeItem.subscription.start_date) + '%' }"
                                        aria-valuenow="86" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!--end::Progress-->

                                <!--begin::Description-->
                                <div class="fs-3 text-gray-600 font-semibold mb-10 flex gap-4 w-full">
                                    <span class="w-full" v-text="activeItem.subscription.start_date"></span>
                                    <span class="flex-none" v-text="activeItem.subscription.end_date"></span>
                                </div>
                                <!--end::Description-->

                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Card body-->


                    <!--begin::Card body-->
                </div>
                <div class="card  mb-5 mb-xl-10">
                    <div class="card-body">
                        <div class="card-title p-6">
                            <h3 class="m-0 text-gray-800" v-text="translate('Latest invoices')"></h3>
                        </div>
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <!--begin::Thead-->
                            <thead class="border-gray-200 fs-5 font-semibold bg-lighten">
                                <tr>
                                    <th class="min-w-125px px-9" v-text="translate('Invoice')"></th>
                                    <th class="min-w-150px " v-text="translate('Transaction')"></th>
                                    <th class="min-w-150px " v-text="translate('Date')"></th>
                                    <th class="min-w-150px" v-text="translate('Package')"></th>
                                    <th class="min-w-125px" v-text="translate('Amount')"></th>
                                    <th class="min-w-125px" v-text="translate('Status')"></th>
                                </tr>
                            </thead>
                            <!--end::Thead-->

                            <!--begin::Tbody-->
                            <tbody class="fs-3 font-semibold text-gray-600" v-if="activeItem.invoices">
                                <tr v-for="invoice in activeItem.invoices">
                                    <td class="px-9" v-text="invoice.code"></td>
                                    <td class=""> <span
                                            v-text="invoice.transaction ? invoice.transaction.transaction_id : ''"></span>
                                        | <span
                                            v-text="invoice.transaction && invoice.transaction.field ? invoice.transaction.field.id : ''"></span>
                                    </td>
                                    <td class="" v-text="invoice.date"></td>
                                    <td class=""> <span
                                            v-text="invoice.item && invoice.item.package ? invoice.item.package.name : ''"></span>
                                    </td>
                                    <td class="" v-text="'$' + invoice.total_amount"></td>
                                    <td :class="(invoice.total_amount < 1 || invoice.status == 'paid') ? 'text-success' : 'text-danger'"
                                        v-text="translate((invoice.total_amount > 0 ? invoice.status : 'Free'))"></td>
                                </tr>
                            </tbody>
                            <!--end::Tbody-->
                        </table>
                    </div>
                </div>
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
import { translate, getProgressWidth, formatCustomTime, toHHMMSS, getPercentageBetweenDates, deleteByKey } from '@/utils.vue';

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

            const tabs = ref([translate('Info'), translate('Stats'), translate('Comments'), translate('Subscription')]);

            const back = () => {
                emit('callback');
            }

            const removeCustomer = () => {
                return deleteByKey('customer_id', activeItem.value, 'Customer.delete', translate('Deactivate this customer and disable its all related items'))
            }

            const progressWidth = () => {
                let requiredData = ['name', 'email', 'status'];

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
                removeCustomer,
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
                getPercentageBetweenDates,
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