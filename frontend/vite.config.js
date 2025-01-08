import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
  base: '/', // Hier geef je het base pad aan als je frontend in een submap zit
  plugins: [vue()],
  server: {
    cors: true, 
  },
});
