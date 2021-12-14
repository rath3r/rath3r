// const express = require('express')
// const app = express()
// const port = 3004
const config = require('./webpack.config.js');
const webpack = require('webpack');
const compiler = webpack(config);

// `compiler.run()` doesn't support promises yet, only callbacks
await new Promise((resolve, reject) => {
    compiler.run((err, res) => {
      if (err) {
        return reject(err);
      }
      resolve(res);
    });
  });

//app.use(express.static('public'))

// app.get('/', (req, res) => {
//   res.send('Hello World!')
// })

// app.listen(port, () => {
//   console.log(`Example app listening at http://localhost:${port}`)
// })


// https://masteringjs.io/tutorials/webpack/programmatic-watch