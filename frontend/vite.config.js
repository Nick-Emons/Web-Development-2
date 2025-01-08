import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
  base: '/frontend/', // Hier geef je het base pad aan als je frontend in een submap zit
  plugins: [vue()],
  server: {
    cors: true, // Hiermee staat Vite CORS toe en voegt automatisch 'Access-Control-Allow-Origin: *' toe
  },
});
