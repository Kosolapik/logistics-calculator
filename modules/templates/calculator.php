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
         <h1 class="title-h1 title-calculator">Расчёт стоимости доставки</h1>
         <form class="form form-calculator" method="post">
            <h2 class="title-h2 form-calculator__title">Маршрут грузоперевозки</h2>
            <div class="form-calculator__block">
               <div class="form-calculator__cell from-calculator__cell_from">
                  <lable class="form__lable" for="from-locality">От куда</lable>
                  <p class="form-calculator__link-open-field link_from-region" for="from-region">
                     Уточнить регион
                  </p>
                  <input
                     class="form__input display_off"
                     type="text"
                     placeholder="Регион"
                     list="from-region"
                     name="from-region"
                  />
                  <datalist id="from-region"></datalist>
                  <input
                     class="form__input"
                     type="text"
                     placeholder="Населённый пункт"
                     list="from-locality"
                     name="from-locality"
                  />
                  <datalist id="from-locality"></datalist>
               </div>
               <div class="form-calculator__cell from-calculator__cell_where">
                  <lable class="form__lable" for="where-region">Куда</lable>
                  <p class="form-calculator__link-open-field link_where-region" for="where-region">
                     Уточнить регион
                  </p>
                  <input
                     class="form__input display_off"
                     type="text"
                     placeholder="Регион"
                     list="where-region"
                     name="where-region"
                  />
                  <datalist id="where-region"> </datalist>
                  <input
                     class="form__input"
                     type="text"
                     placeholder="Населённый пункт"
                     list="where-locality"
                     name="where-locality"
                  />
                  <datalist id="where-locality"> </datalist>
               </div>
            </div>
            <h2 class="title-h2 form-calculator__title">Параметры груза</h2>
            <div class="form-calculator__block">
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
            <h2 class="title-h2 form-calculator__title">Информация о грузе</h2>
            <div class="form-calculator__block">
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
            <input
               class="form__submit form-calculator__submit"
               type="submit"
               value="Расчитать"
            />
         </form>
         <pre>
         <section class="showData"></section>
         </pre>
      </main>
      <footer class="footer">
      </footer>
   </body>
   <script src="js/app.min.js"></script>
</html>
