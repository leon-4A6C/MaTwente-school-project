var gulp = require('gulp');
var sass = require('gulp-sass');
var browserSync = require("browser-sync");
var autoprefixer = require("gulp-autoprefixer");
var uncss = require("gulp-uncss");
var php = require("gulp-connect-php");

gulp.task("watch", ["sass", "browserSync"],function() {
  gulp.watch("src/styles/**/*.*", ["sass"]);
  gulp.watch("src/javascript/**/*.js", browserSync.reload);
  gulp.watch("src/*.html", browserSync.reload);
  gulp.watch("src/*.php", browserSync.reload);
});

gulp.task('sass', function(){
  return gulp.src('src/styles/main.sass')
    .pipe(sass().on("error", sass.logError)) // Using gulp-sass
    .pipe(autoprefixer())
    // .pipe(uncss({
    //   html: ["src/**/*.html", "src/**/*.php"]
    // }))
    .pipe(gulp.dest('src/styles'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

gulp.task("php", function() {
  php.server({base: "src",
   port: 3000,
   keepalive: true,
   bin:'c:/xampp/php/php.exe',
   ini:'c:/xampp/php/php.ini',
   base:'./www'});
});

gulp.task('browserSync', ["php"], function() {
  browserSync({
    proxy: "localhost:3000",
    open: true,
    notify: false
  })
});
