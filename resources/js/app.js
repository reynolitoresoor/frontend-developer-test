import './bootstrap';
import '../sass/app.scss'
import Router from './router'
import store from './store'
import { createApp } from 'vue';
const app = createApp({})
app.use(Router)
app.use(store)
app.mount('#app')