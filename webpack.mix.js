const mix = require('laravel-mix');
const path = require('path');
const { exec } = require('child_process');

mix.alias({
    ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
});

mix.extend('ziggy', new class {
    register(config = {}) {
        this.watch = config.watch ?? ['routes/*.php'];
        this.path = config.path ?? '';
        this.enabled = true;//config.enabled ?? !Mix.inProduction();
    }

    boot() {
        if (!this.enabled) return;

        const command = () => exec(
            `php artisan ziggy:generate ${this.path}`,
            (error, stdout, stderr) => console.log(stdout)
        );

        command();

        if (Mix.isWatching() && this.watch) {
            ((require('chokidar')).watch(this.watch,{usePolling: true}))
                .on('all', (path) => {
                    console.log(`${path} changed...`);
                    command();
                });
        };
    }
}());
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/frontend/app.js', 'public/assets/js/frontend')
    .js('resources/js/app.js', 'public/assets/js')
    .js('resources/js/jsvalidation.js', 'public/assets/js')
    .ziggy();
mix.copyDirectory("resources/images", "public/assets/images");
mix.copyDirectory("resources/fonts", "public/assets/fonts");
mix.copyDirectory("resources/css", "public/assets/css");

mix.sass(
        "resources/scss/frontend/frontend.scss",
        "public/assets/css/frontend/frontend.css"
    )
    .options({
        processCssUrls: false,
    });
mix.scripts(
    [
        'resources/js/frontend/bundle/jquery.min.js',
        'resources/js/frontend/bundle/bootstrap.bundle.min.js',
        'resources/js/frontend/bundle/nioapp.min.js',
        'resources/js/frontend/bundle/simplebar.min.js',
        'resources/js/frontend/bundle/select2.full.min.js',
        'resources/js/frontend/bundle/sweetalert2.min.js',
        'resources/js/frontend/bundle/Chart.min.js',
        /** DataTable */
        'resources/js/frontend/bundle/jquery.dataTables.min.js',
        'resources/js/frontend/bundle/dataTables.bootstrap4.min.js',
        /** Date Picker */
        'resources/js/frontend/bundle/bootstrap-datepicker.min.js',
        'resources/js/jsvalidation.js',
        'resources/js/frontend/common.js',
        'resources/js/frontend/scripts.js',
        'resources/js/cropper.min.js',
        ],
        "public/assets/js/frontend/complied-app.js"
    );
mix.js(
    ["resources/js/frontend/auth/login.js"],
    "public/assets/js/frontend/auth/login.js"
)
.js(
["resources/js/frontend/auth/register.js"],
    "public/assets/js/frontend/auth/register.js"
)
;
mix.scripts(
    [
        'resources/js/frontend/bundle/jquery.min.js',
        'resources/js/frontend/bundle/bootstrap.bundle.min.js',
        'resources/js/jsvalidation.js',
        'resources/js/frontend/common.js',
        'resources/js/cropper.min.js',
        ],
        "public/assets/js/frontend/frontend-app.js"
    );

mix.version();
