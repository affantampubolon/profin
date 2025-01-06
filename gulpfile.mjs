import gulp, { series, src, dest, watch } from "gulp";
import gulpSass from "gulp-sass";
import * as sass from "sass";
const sassCompile = gulpSass(sass);
import autoprefixer from "autoprefixer";
import postcss from "gulp-postcss";
import pug from "gulp-pug"; // Make sure to install: npm install gulp-pug
import browserSync from "browser-sync";

const bs = browserSync.create();
const processors = [autoprefixer()];

//scss to css
function style() {
  return src("./public/riho/assets/scss/**/*.scss", { sourcemaps: true })
    .pipe(sassCompile().on("error", sassCompile.logError))
    .pipe(postcss(processors)) // Use postcss with autoprefixer
    .pipe(dest("./public/css", { sourcemaps: "." })) // Output to public/css
    .pipe(bs.stream());
}

// Watch function
function watcher() {
  // Renamed to watcher to avoid conflict with gulp.watch
  bs.init({
    proxy: "http://localhost:8080",
  });
  watch("./public/riho/assets/scss/**/*.scss", style);
  // watch("./public/riho/assets/pug/pages/template/**/*.pug", html);
  watch("./public/css/*.css", bs.reload); // Watch compiled CSS
  watch("./public/template/*.html", bs.reload); // Watch compiled HTML
  watch(["app/**/*.php", "app/Views/**/*"], bs.reload); // Combined PHP/View watch
}

// Build task (for production)
function build() {
  return series(style, html); // Runs style and html in series
}

// Default task
export default series(style, watcher); // Runs build then watch

// Export individual tasks for use on command line if needed.
export { style, watcher, build };
