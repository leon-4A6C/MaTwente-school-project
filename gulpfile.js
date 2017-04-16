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
    .pipe(sass()) // Using gulp-sass
    .pipe(autoprefixer())
    .pipe(uncss({
      html: ["src/**/*.html", "src/**/*.php"]
    }))
    .pipe(gulp.dest('src/styles'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

gulp.task("php", function() {
  php.server({base: "src", port: 8010, keepalive: true});
});

gulp.task('browserSync', ["php"], function() {
  browserSync({
    proxy: "localhost:8010",
    port: 8080,
    open: true,
    notify: false
  })
});
