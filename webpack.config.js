module.exports = {
    module: {
        loaders: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            }
        ]
    },
    vue: {
        loaders: {
            js: 'babel-loader'
        }
    }
}