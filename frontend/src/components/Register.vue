<template>
    <div class="register-container">
      <h1>Register</h1>
      <form @submit.prevent="register">
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" v-model="name" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" v-model="email" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" v-model="password" required />
        </div>
        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <input type="password" id="password_confirmation" v-model="password_confirmation" required />
        </div>
        <button type="submit">Register</button>
      </form>
      <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
      
      <div class="login-link">
        <p>Already have an account? <router-link to="/login">Login here</router-link></p>
      </div>
    </div>
  </template>
  
  
  <script>
  import api  from '../utils/axios.js'
  import '../assets/Register.css';
  
  export default {
    data() {
      return {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        errorMessage: ''
      };
    },
    methods: {
      async register() {
        try {
          if (this.password !== this.password_confirmation) {
            this.errorMessage = "Passwords do not match!";
            return;
          }
          
          const response = await api.post('/register', {
            name: this.name,
            email: this.email,
            password: this.password
          });
  
          // Redirect naar de loginpagina na een succesvolle registratie
          this.$router.push('/login');
        } catch (error) {
          console.error('Error during registration:', error);
          if (error.response) {
            this.errorMessage = 'Registration failed: ' + error.response.data.message;
          } else {
            this.errorMessage = 'Network error: ' + error.message;
          }
        }
      }
    }
  };
  </script>
  