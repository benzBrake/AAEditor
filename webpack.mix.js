const mix = require("laravel-mix");
require('laravel-mix-clean');
const path = require("path");
mix.webpackConfig({
    resolve: {
        modules: ["node_modules"],
        fallback: {
            path: require.resolve("path-browserify"),
            fs: require.resolve("browserify-fs"),
            stream: require.resolve("stream-browserify"),
            http: require.resolve("stream-http"),
            os: require.resolve("os-browserify/browser"),
            crypto: require.resolve("crypto-browserify"),
            child_process: false,
            net: false,
        },
    },
});
mix.setPublicPath("assets/dist")
    .clean()
    .js("assets/src/main.js", "assets/dist/js")
    .js("assets/src/previewUtils.js", "assets/dist/js")
    .js("assets/src/config/config.js", "assets/dist/js");
mix.sass("assets/src/main.scss", "assets/dist/css")
    .sass("assets/src/editor/x.scss", "assets/dist/css")
    .sass("assets/src/editor/hljs.scss", "assets/dist/css")
    .sass("assets/src/content.scss", "assets/dist/css")
    .sass("assets/src/config/config.scss", "assets/dist/css")
    .copy("assets/src/editor-js.php", "assets/dist/js")
    .copy("node_modules/highlight.js/styles", "assets/dist/css/highlight.js")

if (mix.inProduction()) {
    mix.sourceMaps();
    mix.version();
} else {
    mix.browserSync({
        proxy: "localhost",
        files: ["assets/src/*.*", "assets/src/**/*.*"],
    });
}
