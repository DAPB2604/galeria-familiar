const { defineConfig } = require('cypress');

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost:8080',
    video: false,
    retries: 1,
    defaultCommandTimeout: 8000
  }
});
