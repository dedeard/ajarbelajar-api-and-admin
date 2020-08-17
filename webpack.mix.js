const mix = require("laravel-mix")

mix.js("resources/js/app.js", "public/js")

mix.sass("resources/sass/theme.scss", "public/css/theme.css")
mix.sass("resources/sass/app/app.scss", "public/css/app.css")
mix.sass("resources/sass/auth/auth.scss", "public/css/auth.css")

mix.disableNotifications()
mix.options({
  processCssUrls: false
})

if (!mix.inProduction()) {
  mix.browserSync({
    proxy: "http://127.0.0.1:8000"
  })
} else {
  mix.version()
}
