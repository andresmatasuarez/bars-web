'use strict';

const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const PROJECT_ROOT = __dirname;
const OUTPUT_PATH = path.join(PROJECT_ROOT, 'wp-content/themes/bars2013');

const webpackConfig = {
  context: PROJECT_ROOT,
  entry: {
    main: './assets/javascripts/main.js'
  },
  output: {
    filename: '[name].js', //-[hash]
    path: OUTPUT_PATH
  },
  module: {
    loaders: [
      {
        test: /\.jsx?$/, loader: 'babel', exclude: /node_modules/,
        query: { presets: ['es2015'], plugins: ['add-module-exports'] }
      },
      { test: /\.css$/, loader: ExtractTextPlugin.extract([ 'css-loader' ]) },
      { test: /\.less$/, loader: ExtractTextPlugin.extract([ 'css-loader!less-loader' ]) },
      { test: /\.(png|jpg|gif)$/, loaders: [ 'url?limit=80000', 'img?minimize&optimizationLevel=7' ]},

      { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: 'url?limit=10000&minetype=application/font-woff' },
      { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: 'file' }
    ]
  },
  plugins: [
    new webpack.ProvidePlugin({ $: 'jquery', jQuery: 'jquery' }),
    new ExtractTextPlugin('style.css')
  ]
};

module.exports = webpackConfig;
