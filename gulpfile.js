const{src,dest,watch,series}=require('gulp');
const sass=require('gulp-sass')(require('sass'));
const plumber=require('gulp-plumber');

function js(done){
    src('src/js/app.js')
    .pipe(dest('build/js'));
    done();
}
function css(done){
    //Identificar el archivo sass
    //Compilarlo a CSS
    //Almacenarlo en el disco
    src('src/scss/**/*.scss')
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(dest('build/css'));
    done();
}
function dev(){
    watch('src/scss/**/*.scss',css);
    watch('src/js/**/*.js',js);
}


exports.css=css;
exports.dev=dev;
exports.default=series(js,css,dev);