import { createApp } from 'vue'; // Zorg ervoor dat je Vue import correct is
import App from './App.vue'; // Of de naam van je hoofdcomponent
import ChampionList from './components/ChampionList.vue'; // Importeer je component

const app = createApp(App);

app.component('champion-list', ChampionList); // Registreer je component
app.mount('#app'); // Mount de app
