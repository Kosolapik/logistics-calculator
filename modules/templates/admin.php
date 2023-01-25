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
         <h1 class="title-h1">Кабинет администратора</h1>
         <button class="button button__pec">Сопоставить города ПЭКа</button>
            <section class="showData">
                <pre>
                    <?php
                        if ($cities) {
                            echo count($cities) . '<br>';
                            print_r ($cities); 
                        } else {
                            print_r ('Данных нет');
                        }
                    ?>
                </pre>
            </section>
      </main>
      <footer class="footer">
      </footer>
   </body>
   <script src="js/admin.js"></script>
</html>