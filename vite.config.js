import { defineConfig } from 'vite'

export default defineConfig({
  root: 'app',
  server: {
    port: 3000,
    open: true,
    host: true // Allow access from network if needed
  }
})
