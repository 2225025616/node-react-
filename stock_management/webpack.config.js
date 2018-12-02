var webpack = require('webpack');
var path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    entry: {
        admin: ['babel-polyfill','./src/index.js']
        // 作为外部模块,不打包到webpack的主文件
        // vendor: ['react', 'react-dom'],
    },
    output: {
        path: path.join(__dirname, './public'),
        filename: '[name].[hash].js',
        publicPath: '/'
    },
    devServer: {
      compress: true,
      port: 3002,
      host: '0.0.0.0',
      hot: true,
      historyApiFallback: true,
      publicPath: '/'
    },
    module: {
        rules: [
            {
                test: /\.(css|less)$/,
                use: [
                    'style-loader',
                    'css-loader',
                    { loader: 'less-loader', options: { javascriptEnabled: true }}
                ]
            },
            {
                test: /\.js[x]?$/,
                include: path.resolve(__dirname, "./src"),
                loader: 'babel-loader',
                query: {
                    plugins: [
                    'transform-decorators-legacy',
                    ["import",
                        [{ "libraryName": "antd", "style": true }]
                    ]
                    ],
                    presets: ['es2015', 'stage-0', 'react'],
                    cacheDirectory: true
                }
            },
            { test: /\.(gif|jpg|png)$/, loader: 'url?limit=8192&name=images/[name].[hash].[ext]' },
            { test: /\.(woff|svg|eot|ttf)$/, loader: 'url?limit=50000&name=fonts/[name].[hash].[ext]' }
        ]
    },
    mode: 'development',
    plugins: [
        new webpack.HotModuleReplacementPlugin(),
        new HtmlWebpackPlugin({
            filename: 'index.html',
            template: './views/index.html',
            inject: true
        })
        // new webpack.optimize.UglifyJsPlugin({ compress: { warnings: false } }), // 版本上线时开启
        // new webpack.optimize.OccurenceOrderPlugin(),
        // new webpack.NoErrorsPlugin()
    ]
}
