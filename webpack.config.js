const path = require('path');

module.exports = {
  entry: {
    app: `${__dirname}/index.ts`
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
  },
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'public'),
  },
};