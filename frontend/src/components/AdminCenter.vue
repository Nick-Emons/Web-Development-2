<template>
  <div class="admin-center">
    <div class="back-button-container-admin">
      <button @click="$router.push('/champions')" class="back-button-admin">
        Back to Champion List
      </button>
    </div>

    <div class="header-admin">
      <h1 v-if="!selectedUser">Admin Center</h1>
      <h1 v-else>{{ selectedUser.name }}'s Favorites</h1>


      <p v-if="!selectedUser"> Welcome {{ loggedInUser ? loggedInUser.name : 'Admin' }}, this is the management panel.
      </p>
      <p v-else> Here you can see {{ selectedUser.name }}'s favorite champion(s)</p>
    </div>


    <div v-if="!selectedUser">
      <h2>User List</h2>
      <p v-if="userErrorMessage !== ''" class="error-message">{{ userErrorMessage }}</p>

      <table v-else class="user-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" :class="{ 'logged-in-user': user.id === loggedInUser.id }">
            <td>{{ user.id }}</td>
            <td>
              <span v-if="!user.editing">{{ user.name }}</span>
              <input v-else type="text" v-model="user.name" class="editable-input" />
            </td>
            <td>
              <span v-if="!user.editing">{{ user.email }}</span>
              <input v-else type="text" v-model="user.email" class="editable-input" />
            </td>
            <td>
              <select v-model="user.role" :disabled="user.id === loggedInUser.id || !user.editing">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </td>
            <td>
              <button class="user-buttons" v-if="!user.editing" @click="enableEditing(user)">
                Edit
              </button>
              <button class="user-buttons" v-else @click="saveChanges(user)">Save</button>
              <button class="user-buttons" v-if="user.editing" @click="cancelEditing(user)">
                Cancel
              </button>
              <button v-if="!user.editing" @click="showFavorites(user)">Favorites</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else>
      <button @click="clearFavoritesView" class="back-to-user-list-btn-admin">
        Back to User List
      </button>
      <p v-if="favoriteErrorMessage !== ''" class="error-message">{{ favoriteErrorMessage }}</p>
      <table v-else-if="favorites.length > 0" class="favorites-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Title</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="favorite in favorites" :key="favorite.id">
            <td><img :src="'http://ddragon.leagueoflegends.com/cdn/12.14.1/img/champion/' + favorite.champion.image"
                alt="Champion Image" class="champion-image-admin" @click="goToChampionDetail(favorite.champion.id)" />

            </td>
            <td><span @click="goToChampionDetail(favorite.champion.id)" class="champion-name-admin"> {{
              favorite.champion.name }}</span></td> 
            <td>{{ favorite.champion.title }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import api from "../utils/axios.js";
import '../assets/AdminCenter.css';

export default {
  name: "AdminCenter",
  data() {
    return {
      users: [],
      loggedInUser: null,
      selectedUser: null,
      favorites: [],
      favoriteErrorMessage: '',
      userErrorMessage: '',
    };
  },
  mounted() {
    this.fetchLoggedInUser();
    this.fetchUsers();
  },
  methods: {
    async fetchLoggedInUser() {
      try {
        const response = await api.get("/user");
        this.loggedInUser = response.data;
      } catch (error) {
        console.error("Error fetching logged-in user:", error);
        this.$router.push({ name: "Login" });
      }
    },
    async fetchUsers() {
      try {
        const response = await api.get("/users");
        this.users = response.data.map((user) => ({
          ...user,
          editing: false,
        }));
      } catch (error) {
        console.error("Error fetching favorites:", error);
        this.errorMessage = error.response?.data?.message || 'An unexpected error occurred';
      }
    },
    enableEditing(user) {
      user.editing = true;
    },
    async saveChanges(user) {
      try {
        const updatedUser = {
          name: user.name,
          email: user.email,
          role: user.role,
        };
        const response = await api.put(`/users/${user.id}`, updatedUser);

        user.editing = false;

        alert(response.data.message);
      } catch (error) {
        console.error("Error saving user:", error);
        alert(error.response?.data?.message || 'Something went wrong, while updating the user.');
      }
    },
    cancelEditing(user) {
      this.fetchUsers();
      user.editing = false;
    },
    async showFavorites(user) {
      try {
        this.selectedUser = user;
        const response = await api.get(`/users/${user.id}/favorites`);

        if (response.data && response.data.length > 0) {
          this.favorites = response.data;
          this.errorMessage = "";
        } else {
          this.errorMessage = "No favorites found for this user.";
        }
      } catch (error) {
        console.error("Error fetching favorites:", error);
        this.errorMessage = error.response?.data?.message || 'An unexpected error occurred';
      }
    },
    clearFavoritesView() {
      this.selectedUser = null;
      this.favorites = [];
    },
    goToChampionDetail(championId) {
      this.$router.push({ name: 'ChampionDetail', params: { id: championId } });
    },
  },
};
</script>
