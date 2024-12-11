import { src, dest, watch as __watch, series, task } from "gulp";
import { sass } from "gulp-sass";
// const sass = require("gulp-sass")(require("sass"));
import autoprefixer from "gulp-autoprefixer";
import sourcemaps from "gulp-sourcemaps";
import pug from "gulp-pug";
const browserSync = require("browser-sync").create();

//scss to css
function style() {
  return src("assets/scss/**.scss", { sourcemaps: true })
    .pipe(
      sass({
        //outputStyle: "compressed",
      }).on("error", sass.logError)
    )
    .pipe(autoprefixer("last 2 versions"))
    .pipe(dest("assets/css", { sourcemaps: "." }))
    .pipe(browserSync.reload({ stream: true }));
}

// pug to html
function html() {
  return src("./assets/pug/pages/template/**.pug")
    .pipe(
      pug({
        pretty: true,
      })
    )
    .on("error", console.error.bind(console))
    .pipe(dest("template"))
    .pipe(
      browserSync.reload({
        stream: true,
      })
    );
}
// Watch function
function watch() {
  browserSync.init({
    proxy: "localhost/riho-march/riho_upload_webpack_gulp/template/index.html",
  });
  __watch("assets/scss/**/**.scss", style);
  __watch("./assets/pug/pages/template/**.pug", html);
  __watch("./*.html").on("change", browserSync.reload);
  __watch("assets/css/*.css").on("change", browserSync.reload);
}

const _style = style;
export { _style as style };
const _html = html;
export { _html as html };
const _watch = watch;
export { _watch as watch };

const build = series(watch);
task("default", build, "browser-sync");
