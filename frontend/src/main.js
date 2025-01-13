import { createApp } from 'vue'; 
import App from './App.vue'; 
import router from './router'; 

const app = createApp(App);

// Gebruik de router
app.use(router);

// Mount de app
app.mount('#app');
