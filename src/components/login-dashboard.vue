<template>
    <div class="block w-full overflow-x-auto">
        <main id="login-page" class="   flex-1   w-full">
            <div class="relative w-full pb-8">
                <div class="relative lg:px-4 xl:px-0 mx-auto md:flex items-center gap-8">
                    <div class="login-content container h-screen lg:flex justify-center items-center">
                        <div class="p-2 lg:p-8 bg-white dark:bg-gray-700 rounded-lg  flex-auto">
                            <div class="lg:p-8 p-2 bg-white dark:bg-gray-700 rounded-lg max-w-6xl ">
                                <form disabled="true" action="#!" class=" mx-auto disable p-8 py-0 m-auto rounded-lg max-w-xl pb-10">
                                    <h1 class="m-auto max-w-xl text-center" v-text="translate('login_page')"></h1>
                                    <input v-model="form.type" name="type" type="hidden" value="userLogin">
                                    <input v-model="form.request_type" name="request_type" type="hidden" value="json">
                                    <input v-model="form.email" name="params[email]" type="text" class="h-12 mt-3 rounded w-full border px-3 text-gray-700  focus:border-blue-100 dark:bg-gray-800  dark:border-gray-600" :placeholder="translate('email')">
                                    <input v-model="form.password" name="params[password]" type="password" class="h-12 mt-3 rounded w-full border px-3 text-gray-400  focus:border-blue-100 dark:bg-gray-800 dark:border-gray-600" :placeholder="translate('password')">
                                    <button class="uppercase h-12 mt-3 text-white w-full rounded bg-red-700 hover:bg-red-800" v-text="translate('login')"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
<script>
    
import axios from 'axios'

export default {
    computed: {},
    data() {
        return {
            form: {type:'userLogin', request_type:'json'},
            ItemsList: []
        }
    },
    props: ['title', 'form_action'],
    created: function() {
    },
    methods: {

        /**
         * Handle login access result 
         * 
         */
        handleAccess(response) 
        {
            if (response && response.success == 1)
            {
                this.$alert(response.result).then(() => {
                    location.reload();
                });
            }
            if (response && response.error)
            {
                this.$alert(response.error);
            }
        },

        sendData() {

            const params = new URLSearchParams([]);
            params.append('type', this.form.type);
            params.append('request_type', this.form.request_type);
            params.append('params[email]', this.form.email);
            params.append('params[password]', this.form.password);
            this.handleRequest(params).then(data => { this.handleAccess(data); });
        },
        async handleRequest(params) {

            // Demo json data
            return await axios.post(this.form_action, params.toString()).then(response => {
                if (response.data.status == true)
                    return response.data.result;
                else 
                    return response.data;

            });
        },
        translate(i)
        {
            return this.$parent.translate(i);
        }
    }
};
</script>