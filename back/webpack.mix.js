const mix = require('laravel-mix');
const glob = require('glob');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// エントリーポイント, 出力先 の指定
glob.sync('resources/assets/ts/app/**/*.ts').map(function(file) {
    mix.ts(file, 'public/js').vue();
});

mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.sass('resources/assets/sass/reset.scss', 'public/css');

mix.webpackConfig({
    module: {
        // rules: [
        //     {
        //         test: /\.styl$/,
        //         use: ['style-loader', 'css-loader', 'stylus-loader']
        //     }
        // ]
    },
    optimization: { // ライブラリの分割コンパイル
        splitChunks: {
            chunks: 'all',
            minSize: 0,
            cacheGroups: {
                vendor: {
                    name: 'js/vendor',
                    test: /[\\/]node_modules[\\/]/,
                    priority: -10
                },
                default: false
            }
        }
    }
})

// 本番ではversioningしてブラウザキャッシュを防ぐ
if (mix.inProduction()) {
    mix.version();
}
