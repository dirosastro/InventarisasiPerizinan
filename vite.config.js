import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  root: 'app',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'app/index.html'),
        admin: resolve(__dirname, 'app/admin.html'),
        dashboard: resolve(__dirname, 'app/dashboard.html'),
        login: resolve(__dirname, 'app/login.html'),
        manajemen_user: resolve(__dirname, 'app/manajemen_user.html'),
        perizinan: resolve(__dirname, 'app/perizinan.html'),
        peta: resolve(__dirname, 'app/peta.html'),
        test_wa: resolve(__dirname, 'app/test_wa.html')
      }
    }
  },
  server: {
    port: 3000,
    open: true,
    host: true,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '/api')
      }
    }
  }
})
