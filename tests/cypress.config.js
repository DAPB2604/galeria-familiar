const { defineConfig } = require('cypress')

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost:8080',
    supportFile: 'cypress/support/commands.js',
    video: false,
    retries: 1,
    defaultCommandTimeout: 8000,
  },
})