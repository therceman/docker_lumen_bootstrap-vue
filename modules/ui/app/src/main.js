import { createApp } from 'vue'
import {createStore} from 'vuex'
import router from './router'
import App from './App.vue'

import './css/style.scss'

window.env = process.env;

window.env.API_ORIGIN = window.location.host;

import clientStore from './store/client-store';

// Create a new store instance.
const store = createStore({
    modules: {
        client: clientStore,
    }
})

const app = createApp(App)
app.use(router)
app.use(store)
app.mount('#app')
