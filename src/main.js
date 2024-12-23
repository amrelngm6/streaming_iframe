import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'

const app = createApp(App)

import VueFeather from 'vue-feather';
app.component(VueFeather.name, VueFeather);


app.config.productionTip = false
import $ from "jquery";
window.$ = $;

app.use(createPinia())

app.mount('#app')
