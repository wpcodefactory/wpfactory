var path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');


// change these variables to fit your project
const outputPath = './assets';
const entryPoints = {
    //admin: ['./src/js/admin.js', './src/scss/admin.scss'],
    //frontend: ['./src/scss/frontend.scss']
    frontend: ['./src/scss/frontend.scss', './src/js/frontend.js']
};

// Rules
const rules = [
    {
        test: /\.scss$/i,
        use: [
            MiniCssExtractPlugin.loader,
            {loader: 'css-loader', options: {url: true, sourceMap: true}},
            {
                loader: "postcss-loader",
                options: {
                    postcssOptions: {
                        plugins: [
                            [
                                "postcss-preset-env",
                                {
                                    browsers: 'defaults'
                                },
                            ],
                        ],
                    },
                },
            },
            'sass-loader',
        ]
    },
    {
        test: /\.(png|svg|jpg|jpeg|gif)$/i,
        type: 'asset/resource',
        generator: {
            publicPath: "img/",
            outputPath: 'img',
        },
    },
    {
        exclude: /node_modules/,
        test: /\.jsx?$/,
        loader: 'babel-loader',
        options: {
            presets: ["@babel/preset-env"],
        }
    }
];

// Development
const devConfig = {
    entry: entryPoints,
    output: {
        publicPath: 'auto',
        //publicPath: '/',
        path: path.resolve(__dirname, outputPath),
        filename: 'js/[name].js',
        chunkFilename: 'js/modules/[name].js',
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),

        // Uncomment this if you want to use CSS Live reload
        new BrowserSyncPlugin({
            port: 3000,
            proxy: 'http://test.wpdev.com/',
            files: [outputPath + '/css/*.css'],
            injectCss: true,
        }, {reload: false,}),

    ],
    module: {
        rules: rules
    },
    devtool: 'source-map',

};

// Production
const prodConfig = {
    entry: entryPoints,
    output: {
        path: path.resolve(__dirname, outputPath),
        filename: 'js/[name].min.js',
        chunkFilename: 'js/modules/[name].min.js',
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].min.css',
        }),
    ],
    module: {
        rules: rules
    },
    optimization: {
        chunkIds: 'named',
    },

};

// Exports
module.exports = (env, argv) => {
    switch (argv.mode) {
        case 'production':
            return prodConfig;
        default:
            return devConfig;
    }
}