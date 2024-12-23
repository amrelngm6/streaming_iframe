<template>
    <div class="w-full relative">
        <div class="cursor-pointer relative px-4" @click="switchMenu">  
            <span class="absolute top-0 bg-red-400 text-white text-center w-4 h-4 text-xs rounded-full mt-2" v-if="content.new_count" v-text="content.new_count"></span>
            <notification_icon class="mt-4 mx-2" ></notification_icon>
        </div>
        <div class="drop-ul overflow-y-auto h-80 w-80 mx-auto bg-white px-4 py-6 absolute mt-4" v-if="showPopup && content.items">
            <span class="font-semibold pb-4 block " v-if="content.items && content.items.length" v-text="translate('New notifications')"></span>
            <div  v-if="notification && notification.status == 'new'" v-for="(notification, index) in content.items" :key="index" class="w-full hover:bg-gray-50">
                <div v-if="notification" class="hover:text-purple-600 ">
                    <div :class="notification.status == 'new' ? '' : 'text-gray-400'" @click="setRead(notification)" class="cursor-pointer w-full flex  gap-6">
                        <span class="border border-gray-100 block w-10 h-10 pt-1 text-center bg-white shadow-lg rounded hover:bg-blue-100" >
                            <component class="h-auto mx-auto pt-2 w-4" v-if="notification.model_short_name" :is="notification.model_short_name ? notification.model_short_name : notification_icon"></component>
                        </span>
                        <p class="block overflow-hidden w-56" :title="notification.body">
                            <span class="block text-sm w-full font-semibold" v-text="notification.subject"></span>
                            <span class="text-sm whitespace-nowrap" v-text="notification.body"></span>
                        </p>
                    </div>
                    <div class=" relative">
                        <small  :class="notification.status == 'new' ? '' : 'text-gray-400'" v-text="notification.date" class="left-0 mx-1 my-0 right-0 top-2 absolute"></small>
                        <span class="block w-10 h-8 text-center "><span class="border-l mb-2 mx-auto w-1 h-12 "></span></span>
                    </div>
                </div>
            </div>
            <span class="font-semibold pb-4 block " v-if="content.items && content.items.length" v-text="translate('Read notifications')"></span>
            <div v-for="(notification, index) in content.items" :key="index" class="w-full hover:bg-gray-50">
                <div v-if="notification && notification.status != 'new'" class="hover:text-purple-600 ">
                    <div @click="setRead(notification)" class="cursor-pointer w-full flex  gap-6 text-gray-400">
                        <span class="border border-gray-100 block w-10 h-10 pt-1 text-center bg-white shadow-lg rounded hover:bg-blue-100" >
                            <component class="h-auto mx-auto pt-2 w-4" v-if="notification.model_short_name" :is="notification.model_short_name ? notification.model_short_name : notification_icon"></component>
                        </span>
                        <p class="block overflow-hidden w-56" :title="notification.body">
                            <span class="block text-sm w-full font-semibold" v-text="notification.subject"></span>
                            <span class="text-sm whitespace-nowrap" v-text="notification.body"></span>
                        </p>
                    </div>
                    <div class=" relative">
                        <small v-text="notification.date" class="text-gray-400 left-0 mx-1 my-0 right-0 top-2 absolute"></small>
                        <span class="block w-10 h-8 text-center "><span class="border-l mb-2 mx-auto w-1 h-12 "></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import notification_icon from '@/components/svgs/notification.vue'
import expense from '@/components/svgs/expense.vue'
import device from '@/components/svgs/device.vue'
import customer from '@/components/svgs/customer.vue'
import orderdevice from '@/components/svgs/orderdevice.vue'
import {translate, handleGetRequest, handleRequest} from '@/utils.vue';

export default 
{
    components: {
        orderdevice,
        notification_icon,
        customer,
        device,
        expense,
    },
    name:'plans',
    data() {
        return {
            url: this.conf.url+'admin/latest_notifications?load=json',
            content: {
                title: '',
                new_count:0,
                last_id:0,
                items: [],
                new_items: [],
            },

            activeItem:{},
            showPopup:false,
            showAddSide:false,
            showEditSide:false,
            showLoader: true,
        }
    },

    props: [
        'path',
        'langs',
        'setting',
        'conf',
        'auth',
    ],
    mounted() 
    {
        this.load()

        var t = this;
        setInterval(function(e){
            t.checkNew()
        }, 20000)
    },

    methods: 
    {
        /**
         * handleNotifications
         *
         */
        handleNotifications(data)
        {
            if (data && data.items)
            {
                for (var i = data.items.length - 1; i >= 0; i--) 
                {
                    this.notify(data.items[i].subject, data.items[i].body, data.items[i])
                }
            }
        },


        /**
         * Set Notification as read
         */
        checkNew()
        {
            var params = new URLSearchParams();
            params.append('params[last_id]', this.content.last_id)
            handleRequest(params, '/admin/check_notification' ).then(response=> {
                if (response)
                    this.setValues(response).handleNotifications(response)

            });
        } ,

        /**
         * Set Notification as read
         */
        setRead(notification)
        {
            var params = new URLSearchParams();
            params.append('params[id]', notification.id)
            handleRequest(params, '/admin/read_notification' ).then(response=> {
                if (response)
                    this.load()

                if (response && notification.url)
                    window.open(notification.url)
            });
        } ,

        /**
         * Switch status of showing notifications
         * 
         */
        switchMenu()
        {   

            if (this.showPopup)
                this.showPopup = false, this.checkNew()
            else
                this.showPopup = true, this.load()

        }, 

        load()
        {
            handleGetRequest( this.url ).then(response=> {
                this.setValues(response)
                // this.$alert(response)
            });
        },
        
        notify(subject, body, data = {})
        {

            let t = this;
            if (Notification.permission === "granted") 
            {
                const notification = new Notification(subject, {
                    body: body,
                    data:data,
                    tag: data.id + data.status,
                    icon: this.setting.logo
                });

                notification.onclick = (e) => {
                    let notificationData = e.currentTarget.data ? e.currentTarget.data : {};
                    window.location.href = '/dashboard';
                };

                notification.onclose = (e) => {
                };
                
            }
        },
        setValues(data) {
            this.content = JSON.parse(JSON.stringify(data)); return this
        },
        translate(i)
        {
            return translate(i);
        }
    }
};
</script>
<style lang="css">
    .rtl #side-cart-container
    {
        right: auto;
        left:0;
    }
</style>