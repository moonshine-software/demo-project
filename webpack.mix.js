const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    //.vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ])
    .sass('resources/sass/main.sass', 'public/css')
    .version()
    .sourceMaps();
