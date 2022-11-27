<!DOCTYPE html>
<html lang="ru">
   <head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Главная</title>
</head>
   <body>
      <main>
         <h1>Расчёт стоимости доставки</h1>
         <form class="form form-calculator" method="post">
            <div class="form-calculator__block">
               <h2 class="title-h2 form-calculator__title">Маршрут грузоперевозки</h2>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="from">От куда</lable>
                  <input class="form__input" type="text" name="from" />
               </div>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="where">Куда</lable>
                  <input class="form__input" type="text" name="where" />
               </div>
            </div>
            <div class="form-calculator__block">
               <h2 class="title-h2 form-calculator__title">Параметры груза</h2>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="weight">Общий вес, кг</lable>
                  <input class="form__input" type="number" step="0.1" name="weight" />
               </div>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="volume"
                     >Общий объём, м<sup><small>3</small></sup></lable
                  >
                  <input class="form__input" type="number" step="0.1" name="volume" />
               </div>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="max-size">Макс. габарит, м</lable>
                  <input class="form__input" type="number" step="0.1" name="max-size" />
               </div>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="quantity">Кол-во мест</lable>
                  <input class="form__input" type="number" step="1" name="quantity" />
               </div>
            </div>
            <div class="form-calculator__block">
               <h2 class="title-h2 form-calculator__title">Информация о грузе</h2>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="price">Объявленная стоимость, ₽</lable>
                  <input class="form__input" type="number" step="500" name="price" />
               </div>
               <div class="form-calculator__cell">
                  <lable class="form__lable" for="type-cargo">Характер груза</lable>
                  <input class="form__input" type="text" name="type-cargo" />
               </div>
            </div>
            <div class="form-calculator__block"></div>
            <input class="form__submit" type="submit" value="Расчитать" />
         </form>
         <section class="showData"></section>
      </main>
      <footer class="footer">
      </footer>
   </body>
   <script src="js/app.min.js"></script>
</html>
