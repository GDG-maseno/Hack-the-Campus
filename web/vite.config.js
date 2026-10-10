import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Dev server proxies API + uploads to the Express backend on :3000
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost:3000',
      '/uploads': 'http://localhost:3000',
    },
  },
  build: {
    outDir: 'dist',
  },
});
