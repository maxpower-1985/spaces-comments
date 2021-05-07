// webpack.config.js
const webpack = require("webpack");
const VueLoaderPlugin = require('vue-loader/lib/plugin')
var LiveReloadPlugin = require('webpack-livereload-plugin');
var cloneDeep = require('lodash.clonedeep');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const VueMoment = require('vue-moment');
const moment = require('moment');

var defaults = {

	resolve: {
		alias: {
			'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
		},
		extensions: ['.ts', '.js', '.vue', '.json']
	},
	entry: {
		'post-comment-section': ['./src/base.js']
		//some_foo: ['./src/some-test-foo.vue']
	},
	output: {
		path: __dirname + "/dist",
		filename: "[name].js"
	},
	mode: 'development',
	//mode: 'production',
	externals: {
		vue: "Vue"
	},
	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
			// this will apply to both plain `.js` files
			// AND `<script>` blocks in `.vue` files
			{
				test: /\.js$/,
				loader: 'babel-loader'
			},
			// this will apply to both plain `.css` files
			// AND `<style>` blocks in `.vue` files
			{
				test: /\.css$/,
				use: [
					'vue-style-loader',
					'css-loader'
				]
			},
			{
			// this will apply to `.png, jpg, gif, svg` files
			//publicPath: '/app/themes/defaultspace/img/',
				test: /\.(png|jpg|gif|svg)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: '../../../plugins/spaces-comments/inc/img/',
							publicPath: '/app/plugins/spaces-comments/inc/img/',
							esModule: false,
						}
					}
				],
			}
		]
	},
	plugins: [
		new VueLoaderPlugin(),
		//(new foundation(),
		new LiveReloadPlugin({
			appendScriptTag: true,
			hostname: 'localhost'
		}),
		/*new MomentLocalesPlugin({
				localesToKeep: ['en','de', 'es'],
		})*/
		new MomentLocalesPlugin(),
	]
}

var minimized = cloneDeep(defaults);
minimized.mode = 'production';
minimized.output.filename = "[name].min.js";
module.exports = [defaults, minimized];
