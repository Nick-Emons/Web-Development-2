import axios from "axios";
import router from "../router";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL, 
});

// Interceptor voor het toevoegen van de JWT-token aan de request headers
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("jwt_token");
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor voor het afhandelen van verlopen tokens (401 Unauthorized)
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response && error.response.status === 401) {
      const refreshToken = localStorage.getItem("refresh_token");
      if (refreshToken) {
        try {
          // Verstuur de refresh token naar de backend om een nieuwe access token te verkrijgen
          const refreshResponse = await api.post(
            "/refresh-token", 
            { refresh_token: refreshToken }
          );

          // Bewaar de nieuwe access token
          localStorage.setItem("jwt_token", refreshResponse.data.access_token);

          error.config.headers["Authorization"] = `Bearer ${refreshResponse.data.access_token}`;
          return axios(error.config);
        } catch (refreshError) {
          console.error("Token refresh failed:", refreshError);
          // Verwijder beide tokens en stuur naar de login pagina
          localStorage.removeItem("jwt_token");
          localStorage.removeItem("refresh_token");
          router.push("/login"); // Stuurt de gebruiker naar de login pagina
        }
      } else {
        // Als er geen refresh token beschikbaar is, terug naar login pagina
        router.push("/login");
      }
    }
    return Promise.reject(error);
  }
);

export default api;
