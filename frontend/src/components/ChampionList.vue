<template>
  <div class="champions-container">
    <div class="overlay"></div>

    <div class="admin-button" v-if="isAdmin">
      <router-link to="/admin" class="admin-link">Admin Center</router-link>
    </div>

    <div class="logout-button" v-if="user">
      <button @click="logout">Logout</button>
    </div>

    <h1 class="champions-title">Champions</h1>

    <button @click="toggleFavoriteView" class="favorite-view-button">
      {{ showFavoritesOnly ? 'Show All Champions' : 'Show Favorites' }}
    </button>

    <input type="text" v-model="searchQuery" placeholder="Search for a champion" class="champion-search" />

    <p v-if="filteredChampions.length === 0 && errorMessage == ''" class="no-results">No champions found</p>
    <p v-if="errorMessage !== ''" class="no-results">{{ errorMessage }}</p>

    <ul class="champions-grid">
      <li v-for="champion in filteredChampions" :key="champion.id" class="champion-card"
        @click="goToChampionDetail(champion.id)">
        <img :src="'http://ddragon.leagueoflegends.com/cdn/12.14.1/img/champion/' + champion.image" alt="Champion Image"
          class="champion-image" />
        <h2 class="champion-name">{{ champion.name }}</h2>
        <p class="champion-title">{{ champion.title }}</p>
        <p class="champion-blurb">{{ champion.blurb }}</p>

        <!-- Favorite button -->
        <button :class="['favorite-button', { 'is-favorite': isFavorite(champion.id) }]"
          @click.stop="toggleFavorite(champion.id)">
          {{ isFavorite(champion.id) ? 'Remove from Favorites' : 'Add to Favorites' }}
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
import api from '../utils/axios.js'
import '../assets/ChampionList.css';

export default {
  data() {
    return {
      champions: [],
      favorites: [],
      searchQuery: '',
      user: null,
      errorMessage: '',
      showFavoritesOnly: false,  
    };
  },
  mounted() {
    this.fetchChampions();
    this.fetchUserData();
  },
  methods: {
    async fetchChampions() {
      const isFirstSession = localStorage.getItem('first_session') === 'true';

      try {
        const config = {
          headers: {}
        };

        if (isFirstSession) {
          config.headers['First-Session'] = 'true';
        }

        const response = await api.get('/champions', config);
        this.champions = response.data;

        if (isFirstSession) {
          localStorage.setItem('first_session', 'false');
        }

        this.syncFavorites();
      } catch (error) {
        console.error("There was an issue fetching champions:", error);
        this.errorMessage = error.response?.data?.message;
      }
    },

    async fetchUserData() {
      const token = localStorage.getItem('jwt_token');
      if (token) {
        try {
          const response = await api.get('/user', {
            headers: { Authorization: `Bearer ${token}` },
          });
          this.user = response.data;
        } catch (error) {
          console.error('Error fetching user data:', error);
          localStorage.removeItem('jwt_token');
          this.$router.push('/login');
        }
      }
    },

    logout() {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('favorites');
      localStorage.removeItem('refresh_token');
      localStorage.setItem('first_session', 'true');
      this.user = null;
      this.$router.push('/login');
    },

    syncFavorites() {
      const storedFavorites = JSON.parse(localStorage.getItem('favorites') || '[]');
      this.fetchUserFavorites();
    },

    async fetchUserFavorites() {
      const token = localStorage.getItem('jwt_token');
      if (token) {
        try {
          const response = await api.get('/favorites', {
            headers: { Authorization: `Bearer ${token}` },
          });
          const userFavorites = response.data.map(favorite => favorite.champion_id);
          localStorage.setItem('favorites', JSON.stringify(userFavorites));
          this.favorites = userFavorites;
          this.updateChampionFavorites();
        } catch (error) {
          console.error('Error fetching favorites:', error);
          this.favorites = [];
        }
      }
    },

    updateChampionFavorites() {
      this.champions = this.champions.map(champion => {
        champion.isFavorite = this.favorites.includes(String(champion.id));
        return champion;
      });
    },

    async toggleFavorite(championId) {
      const token = localStorage.getItem('jwt_token');
      if (!token) {
        this.$router.push({ name: 'Login' });
        return;
      }

      const champion = this.champions.find(c => c.id === championId);
      const isAlreadyFavorite = this.isFavorite(championId);

      try {
        if (isAlreadyFavorite) {
          await api.delete(`/${championId}`, {
            headers: { Authorization: `Bearer ${token}` },
          });
          champion.isFavorite = false;
        } else {
          await api.post(
            '/favorites',
            { champion_id: String(championId) },
            {
              headers: { Authorization: `Bearer ${token}` },
            }
          );
          champion.isFavorite = true;
        }
        this.fetchChampions();
      } catch (error) {
        console.error('Error updating favorites:', error);
      }
    },

    isFavorite(championId) {
      return this.favorites.includes(String(championId));
    },

    goToChampionDetail(championId) {
      this.$router.push({ name: 'ChampionDetail', params: { id: championId } });
    },

    toggleFavoriteView() {
      this.showFavoritesOnly = !this.showFavoritesOnly;
    },
  },

  computed: {
    filteredChampions() {
      let championsToShow = this.champions;

      if (this.showFavoritesOnly) {
        championsToShow = championsToShow.filter(champion =>
          this.favorites.includes(String(champion.id))
        );
      }

      const query = this.searchQuery.toLowerCase();
      return championsToShow.filter(champion =>
        champion.name.toLowerCase().includes(query)
      );
    },

    isAdmin() {
      return this.user && this.user.role === 'admin';
    },
  },
};
</script>


