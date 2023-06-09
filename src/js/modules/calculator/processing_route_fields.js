

export function processing_route_fields(select_cell, seleect_obj) {
    let cell = document.querySelector(select_cell);
    let innerRegion = cell.querySelector('.form__inner_region'),
        inputRegion = innerRegion.querySelector('.form__input'),
        blockHintsRegion = innerRegion.querySelector('.form__hints');
    let innerLocality = cell.querySelector('.form__inner_locality'),
        inputLocality = innerLocality.querySelector('.form__input'),
        blockHintsLocality = innerLocality.querySelector('.form__hints');

    let valueRegion = {};
    let valueLocality = {};

    outputHints(inputRegion, blockHintsRegion, "region", 10);
    outputHints(inputLocality, blockHintsLocality, "city", 15);
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
        function  cleanHints(select) {
            let hints = select.querySelectorAll('.form__hints-element');
            if (hints.length > 0) {
                for (let i = 0; i < hints.length; i++) {
                    hints[i].remove();
                }
            }
        }

        /*
            Отслеживаем событие ввода текста и обрабатываем его.
            1. Удаляем все старые подсказки
            2. Если введено 3 и более символов, формируем запрос на сервер и отправляем его.
            3. При получении ответа, создаем новые подсказки и выводим их.
        */
        input.addEventListener('input', function(e) {
            if (input.value.length > 2) {
                // Собираем объект с параметрами для запроса и из собранных параметров формируем строку с get параметрами запроса.
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

                let url = `http://logist-master/api/hints`;
                let ajax = new XMLHttpRequest();
                ajax.open("post", url);
                ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                ajax.onload = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        // console.log(this.response);
                        cleanHints(blockHints);
                        let res = JSON.parse(this.response)['result'];
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
                                    cleanHints(blockHintsLocality);
                                    if (select_cell == ('.form__cell_from')) {
                                        seleect_obj.fromRegion = valueRegion;
                                    } else if (select_cell == ('.form__cell_where')) {
                                        seleect_obj.whereRegion = valueRegion;
                                    }
                                } else if (searchContent == "city") {
                                    input.value = res[i + 1]['typeShort'].toLowerCase() + ". " + res[i + 1]['name'];
                                    valueLocality = res[i + 1];
                                    cleanHints(blockHints);
                                    if (select_cell == ('.form__cell_from')) {
                                        seleect_obj.fromLocality = valueLocality;
                                    } else if (select_cell == ('.form__cell_where')) {
                                        seleect_obj.whereLocality = valueLocality;
                                    }
                                }
                                console.log(seleect_obj);
                            })
                        }
                    }
                };
                ajax.send(getParams);
            } else {
                cleanHints(blockHints);
            }
        });
    }
}
