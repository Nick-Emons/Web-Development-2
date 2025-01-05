<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="login">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <button type="submit">Login</button>
    </form>
    
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    
    <div class="register-link">
      <p>Don't have an account yet? <router-link to="/register">Register here</router-link></p>
    </div>
  </div>
</template>

<script>
import api  from '../utils/axios.js'
import '../assets/Login.css';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      try {
        const response = await api.post('/login', {
          email: this.email,
          password: this.password
        });

        // Slaat de tokens in localStorage op
        localStorage.setItem("jwt_token", response.data.token);
        localStorage.setItem("refresh_token", response.data.refresh_token);

        // Na het inloggen wordt "first_session" op true gezet. Zodat de eerste call om champions op te halen, via de externe API wordt.
        if (!localStorage.getItem('first_session')) {
          localStorage.setItem('first_session', 'true');
        }

        this.$router.push('/champions');
      } catch (error) {
        this.errorMessage = error.response?.data?.message;
        console.error(error);
      }
      
    }
  }
};
</script>


