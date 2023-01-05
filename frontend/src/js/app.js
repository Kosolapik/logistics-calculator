let formDataCalculator = new FormData();

function processingRouteFields(selectCell) {
    let cell = document.querySelector(selectCell);
    let innerRegion = cell.querySelector('.form__inner_region'),
        inputRegion = innerRegion.querySelector('.form__input'),
        blockHintsRegion = innerRegion.querySelector('.form__hints');
    let innerLocality = cell.querySelector('.form__inner_locality'),
        inputLocality = innerLocality.querySelector('.form__input'),
        blockHintsLocality = innerLocality.querySelector('.form__hints');

    let valueRegion = {};
    let valueLocality = {};

    outputHints(inputRegion, blockHintsRegion, "region", 10);
    outputHints(inputLocality, blockHintsLocality, "city", 20);
    /* 
        Функция вывода подсказок при вводе текста.
        Принимает в параметрах:
            - поле ввода текста
            - блок для вывода подсказок
            - что ищем (регион или населнный пункт)
    */
    function outputHints(input, blockHints, searchContent, answerLimit) {
        /*
            Функция открывающая поле региона при клике на ссылку "Указать регион" и скрывающая ссылку.
        */
        (function () {
            let link = innerRegion.querySelector('.form__link');
            link.addEventListener('click', (e) => {
                inputRegion.classList.remove('display_off');
                link.classList.add('display_off');
                inputRegion.focus();
            });
        }());
        /*
            Функция отслеживает фокус на поле ввода.
            Если фокус исчезает, подсказки скрываются.
            Если фокус устанавливается, подсказки появляются.
        */
            (function () {
                input.addEventListener('focus', (e) => {
                    blockHints.classList.remove('display_off');
                });
                input.addEventListener('blur', (e) => {
                    blockHints.classList.add('display_off');
                });
                input.parentElement.tabIndex = 0;
                input.parentElement.addEventListener('focus', (e) => {
                    input.focus(); 
                });
            }());

        /*
            Функция для удаления подсказок.
            В качестве параметра принимает блок с подсказками.
        */
        function cleanHints(select) {
            let hints = select.querySelectorAll('.form__hints-element');
            if (hints.length > 0) {
                for (let i = 0; i < hints.length; i++) {
                    hints[i].remove();
                }
            };
        }

        /*
            Отслеживаем событие ввода текста и обрабатываем его.
            1. Удаляем все старые подсказки
            2. Если введено 3 и более символов, формируем запрос на сервер и отправляем его.
            3. При получении ответа, создаем новые подсказки и выводим их.
        */
        input.addEventListener('input', function(e) {
            cleanHints(blockHints);

            if (input.value.length > 2) {
                // Собираем объект с данными для запроса и из собранных данных формируем строку с get параметрами запроса.
                let objectQuery = {};
                objectQuery = {
                    query: input.value,
                    contentType: searchContent,
                    withParent: 1,
                    limit: answerLimit
                };
                if (searchContent == "city" && valueRegion.id != null) {
                    objectQuery.regionId = +valueRegion.id;
                }
                let getParams = '';
                for (let key in objectQuery) {
                    if (getParams === '') {
                        getParams = `${key}=${objectQuery[key]}`;
                    } else {
                        getParams += `&${key}=${objectQuery[key]}`;
                    }
                }

                let ajax = new XMLHttpRequest();
                let url = 'http://logist-master/api/hints?' + getParams;
                ajax.open('get', url, false);
                ajax.addEventListener('readystatechange', function () {
                    if (this.readyState == 4 && this.status == 200) {
                        let res = JSON.parse(this.responseText)['result'];
                        for (let i = 1; i < res.length; i++) {
                            let hintElement = document.createElement('div');
                            hintElement.classList.add('form__hints-element');

                            if (searchContent == "region") {
                                let hintRegion = document.createElement('div');
                                hintRegion.classList.add('form__hints-region');
                                hintRegion.innerText = res[i]['name'] + " " + res[i]['type'].toLowerCase();
                                hintElement.append(hintRegion);
                            } else if (searchContent == "city") {
                                let hintCity = document.createElement('div');
                                hintCity.classList.add('form__hints-city');
                                hintCity.innerText = res[i]['typeShort'] + ". " + res[i]['name'] + " (" + res[i]['zip'] + ")";
                                hintElement.append(hintCity);
                                let hintRegion = document.createElement('div');
                                hintRegion.classList.add('form__hints-region');
                                hintRegion.innerText = res[i]['parents'][0]['name'] + " " + res[i]['parents'][0]['typeShort'].toLowerCase();
                                if (res[i]['parents'].length > 1) {
                                    hintRegion.innerText += ", " + res[i]['parents'][1]['name'] + " " + res[i]['parents'][1]['typeShort'].toLowerCase() + '.';
                                }
                                hintElement.append(hintRegion);
                            }  
                            blockHints.append(hintElement);
                        }
                        /*
                            На каждый элемент подсказок вешаем событие клика.
                        */
                        let childrens = blockHints.children;
                        for (let i = 0; i < childrens.length; i++) {
                            childrens[i].addEventListener('click', function (e) {
                                if (searchContent == "region") {
                                    input.value = res[i + 1]['name'] + " " + res[i + 1]['type'].toLowerCase();
                                    valueRegion = res[i + 1];
                                    inputLocality.value = '';
                                    cleanHints(blockHints);
                                } else if (searchContent == "city") {
                                    input.value = res[i + 1]['typeShort'].toLowerCase() + ". " + res[i + 1]['name'];
                                    cleanHints(blockHints);
                                    valueLocality = res[i + 1];
                                }
                            })
                        }
                    }
                });
                ajax.send();
            }
        });
    }
}
processingRouteFields('.form__cell_from');
processingRouteFields('.form__cell_where');



