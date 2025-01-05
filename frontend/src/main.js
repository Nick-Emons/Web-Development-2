import { createApp } from 'vue'; // Importeer Vue
import App from './App.vue'; // Je hoofdcomponent
import router from './router'; // Importeer de router

const app = createApp(App);

// Gebruik de router
app.use(router);

// Mount de app
app.mount('#app');
