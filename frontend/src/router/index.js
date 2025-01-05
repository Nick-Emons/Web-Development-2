import { createRouter, createWebHistory } from 'vue-router';
import ChampionList from '../components/ChampionList.vue';
import ChampionDetail from '../components/ChampionDetails.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import AdminCenter from '../components/AdminCenter.vue';
import axios from 'axios';

const routes = [
  { path: '/', name: 'ChampionList',component: ChampionList,alias: '/champions', meta: { requiresAuth: true } },
  { path: '/champions/:id', name: 'ChampionDetail', component: ChampionDetail, meta: { requiresAuth: true } },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/admin', name: 'AdminCenter', component: AdminCenter, meta: { requiresAuth: true, requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Route guard voor beveiligde routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('jwt_token');

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({ path: '/login' });
    } else {
      // Controleer de token geldigheid en haal gebruikersgegevens op
      axios.get('http://localhost:8000/api/user', {
        headers: { Authorization: `Bearer ${token}` },
      })
        .then(response => {
          const user = response.data; // Zorg dat de API user info retourneert
          if (to.matched.some(record => record.meta.requiresAdmin)) {
            // Controleer of de gebruiker een admin is
            if (user.role === 'admin') {
              next();
            } else {
              next({ path: '/' }); // Gebruiker terugsturen naar een andere pagina
            }
          } else {
            next(); // Geen admin nodig, laat doorgaan
          }
        })
        .catch(error => {
          localStorage.removeItem('jwt_token');
          next({ path: '/login' });
        });
    }
  } else {
    next(); // Geen authenticatie vereist
  }
});



export default router;
