var gulp = require('gulp'),
  runSequence = require('run-sequence'),
  sass = require('gulp-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  stripCssComments = require('gulp-strip-css-comments'),
  livereload = require('gulp-livereload'),
  babel = require('gulp-babel'),
  es = require('event-stream');

gulp.task('default', function() {
  runSequence(
    'sass',
    'babel',
    'watch'
  );
});

var riseError = function(err) {
  console.log(err.toString());
  this.emit('end');
};

// Compile scss files to css
gulp.task('sass', function() {
  return gulp.src(['scss/*.scss'])
    .pipe(sass({
      outputStyle: 'compact',
      precision  : 8
    }))
    .pipe(autoprefixer('> 1%'))
    .on('error', riseError)
    .pipe(stripCssComments())
    .pipe(gulp.dest('css'))
    .pipe(livereload())
});

gulp.task('babel', function() {
  return es.concat(
    gulp.src('js/*.es')
      .pipe(babel({presets: ['es2015']}))
      .on('error', riseError)
      .pipe(gulp.dest('js'))
  ).pipe(livereload());
});

// Runs sass task if any scss file changes and autoprefixer if global.css changes
gulp.task('watch', function() {
  livereload.listen();
  gulp.watch(['scss/*.scss'], ['sass']);
  gulp.watch(['js/*.es'], ['babel']);
});
