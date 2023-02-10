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
         <form class="form form-calculator" id="form-calculator" name="formcalculator" method="post">
            <h2 class="title-h2 form__title">Маршрут грузоперевозки</h2>
            <div class="form__block form__block_column">
               <div class="form__cell form__cell_from">
                  <lable class="form__lable" for="from-locality">От куда</lable>
                  <div class="form__inner form__inner_region">
                     <p class="form__link">Указать регион</p>
                     <input
                        class="form__input form__input_region display_off"
                        type="text"
                        placeholder="Регион"
                        name="from-region"
                     />
                     <div class="form__hints form__hints_region"></div>
                  </div>
                  <div class="form__inner form__inner_locality">
                     <input
                        class="form__input form__input_locality"
                        type="text"
                        placeholder="Населённый пункт"
                        name="from-locality"
                     />
                     <div class="form__hints form__hints_locality"></div>
                  </div>
               </div>
               <div class="form__cell form__cell_where">
                  <lable class="form__lable" for="where-locality">Куда</lable>
                  <div class="form__inner form__inner_region">
                     <p class="form__link">Указать регион</p>
                     <input
                        class="form__input form__input_region display_off"
                        type="text"
                        placeholder="Регион"
                        name="where-region"
                     />
                     <div class="form__hints form__hints_region"></div>
                  </div>
                  <div class="form__inner form__inner_locality">
                     <input
                        class="form__input form__input_locality"
                        type="text"
                        placeholder="Населённый пункт"
                        name="where-locality"
                     />
                     <div class="form__hints form__hints_locality"></div>
                  </div>
               </div>
            </div>
            <h2 class="title-h2 form__title">Параметры груза</h2>
            <div class="form__block">
               <div class="form__cell">
                  <lable class="form__lable" for="weight">Общий вес, кг</lable>
                  <input class="form__input" type="number" step="0.1" name="weight" />
               </div>
               <div class="form__cell">
                  <lable class="form__lable" for="volume"
                     >Общий объём, м<sup><small>3</small></sup></lable
                  >
                  <input class="form__input" type="number" step="0.001" name="volume" />
               </div>
               <div class="form__cell">
                  <lable class="form__lable" for="max-size">Макс. габарит, м</lable>
                  <input class="form__input" type="number" step="0.1" name="max-size" />
               </div>
               <div class="form__cell">
                  <lable class="form__lable" for="quantity">Кол-во мест</lable>
                  <input class="form__input" type="number" step="1" name="quantity" />
               </div>
            </div>
            <h2 class="title-h2 form__title">Информация о грузе</h2>
            <div class="form__block">
               <div class="form__cell">
                  <lable class="form__lable" for="price">Объявленная стоимость, ₽</lable>
                  <input class="form__input" type="number" step="500" name="price" />
               </div>
               <div class="form__cell">
                  <lable class="form__lable" for="type-cargo">Характер груза</lable>
                  <input class="form__input" type="text" name="type-cargo" />
               </div>
            </div>
            <div class="form__block"></div>
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
   <script src="js/calculator.js"></script>
</html>
