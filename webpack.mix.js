const mix = require("laravel-mix");
// require('laravel-mix-clean');
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
mix.setPublicPath("assets/dist");
mix.js("assets/src/main.js", "assets/dist/js")
    .js("assets/src/previewUtils.js", "assets/dist/js")
    .js("assets/src/config/config.js", "assets/dist/js");

mix.sass("assets/src/main.scss", "assets/dist/css")
    .sass("assets/src/editor/x.scss", "assets/dist/css")
    .sass("assets/src/content.scss", "assets/dist/css")
    .sass("assets/src/config/config.scss", "assets/dist/css");

mix.copy("assets/src/editor-js.php", "assets/dist/js")

if (mix.inProduction()) {
  mix.sourceMaps();
  mix.version();
} else {
  mix.browserSync({
    proxy: "localhost",
    files: ["assets/src/*.*", "assets/src/**/*.*"],
  });
}
