// const express = require('express')
// const app = express()
// const port = 3004
// const config = require('./webpack.config.js');
// const webpack = require('webpack');
// const compiler = webpack(config);

// `compiler.run()` doesn't support promises yet, only callbacks
// await new Promise((resolve, reject) => {
//     compiler.run((err, res) => {
//       if (err) {
//         return reject(err);
//       }
//       resolve(res);
//     });
//   });

//app.use(express.static('public'))

// app.get('/', (req, res) => {
//   res.send('Hello World!')
// })

// app.listen(port, () => {
//   console.log(`Example app listening at http://localhost:${port}`)
// })


// https://masteringjs.io/tutorials/webpack/programmatic-watch

import express from "express";
import fs from "fs";
import path from "path";

import React from "react";
import ReactDOMServer from "react-dom/server";

import App from "../src/App";

const PORT = 8000;

const app = express();

app.use("^/$", (req, res, next) => {
  fs.readFile(path.resolve("./build/index.html"), "utf-8", (err, data) => {
    if (err) {
      console.log(err);
      return res.status(500).send("Some error happened");
    }
    return res.send(
      data.replace(
        '<div id="root"></div>',
        `<div id="root">${ReactDOMServer.renderToString(<App />)}</div>`
      )
    );
  });
});

app.use(express.static(path.resolve(__dirname, '..', 'build')))

app.listen(PORT, () => {
  console.log(`App launched on ${PORT}`);
});
