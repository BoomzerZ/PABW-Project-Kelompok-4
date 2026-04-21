import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { fetchUser } from './utils/auth';

fetchUser();

const app = createApp(App);
app.use(router);
app.mount('#app');
