import './bootstrap';
import { createApp } from 'vue';
import VueProgressBar from "@aacassandra/vue3-progressbar";
import Swal from 'sweetalert2'
import { createRouter, createWebHistory } from 'vue-router'



//Components
import App from './components/App.vue';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1500,
})



window.Swal = Swal
window.toast = Toast

const Vue = createApp(App);
Vue.use(VueProgressBar, { color: 'rgb(0,107,180)', failedColor: 'red', height: '45px' })

const routes = [
    { path: '/', name: App, component: App, },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) { return savedPosition }
        else { return { top: 0, behavior: 'smooth' } }
    }
})


Vue.use(router)
Vue.mount('#root');


