const path = require('path');

module.exports = {
  entry: {
    app: {
      import: './src/public/scripts/app.js',
    }
  },
  output: {
    filename: 'public/scripts/app.js',
  }
};