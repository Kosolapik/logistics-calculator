import webpack from 'webpack-stream'

// создаём объекты с точками входа (entry) из файлов *.js в папке /src/js
import { readdirSync, statSync } from 'fs';
import path from 'path';

const dirFolder = './src/js/';
let folder = readdirSync(dirFolder);
let arr = {};
for (let i = 0; i < folder.length; i++) {
  let element = dirFolder + folder[i]; // путь к элементу папки
  // console.log(element);
  let typeElement = statSync(element).isFile(); // bool файл или НЕ файл
  // console.log(typeElement);
  let nameElement = path.basename(element, path.extname(element)); // имя файла без расширения
  // console.log(nameElement);
  if (typeElement) {
    arr[nameElement] = element;
  }
}

export const js = () => {
  return app.gulp.src(app.path.src.js, { sourcemaps: app.isDev })
    .pipe(app.plugins.plumber(
      app.plugins.notify.onError({
        title: 'JS',
        message: 'Error: <%= error.message %>',
      })
    ))
    .pipe(webpack({
      mode: app.isBuild ? 'production' : 'development',
      entry: arr,
      output: { filename: '[name].min.js' }
    }))
    .pipe(app.gulp.dest(app.path.build.js))
};




