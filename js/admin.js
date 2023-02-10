"use strict";

let arr;
let button = document.querySelector('.button__pec');
let monitor = document.querySelector('.showData');
let buttonAll;
let buttonsWrite;

    
function outputInfo(selectMonitor, answer) {
    /* Очищаем экран вывода информации */
    selectMonitor.innerText = '';
        /*
            Функция для создания новых элементов с нужными класами.
            Возвращает новый элемент.
        */
            function createElement(tag, addedClass) {
                let element = document.createElement(tag);
                if (typeof(addedClass) == "string") {
                    element.classList.add(addedClass);
                } else {
                    for (let i = 0; i < addedClass.length; i++) {
                        let cl = addedClass[i];
                        element.classList.add(cl);
                    }
                }
                return element;
            }
    
    /*
        Запускаем цикл по пришедшему в ответе массиву.
        И для каждого элемента массива (города) создаём ячеку для вывода информации.
        И заполняем её информацией хранящейся в значении этого элемента.
    */ 
    for (let city in answer) {

        let  cellData = createElement('div', 'cell-data');

        let blockInfo = createElement('div', 'cell-data__block-info');
        cellData.append(blockInfo);

            let title = createElement('div', 'cell-data__title');
            blockInfo.append(title);

                if (answer[city]['database']) {
                    let cityPec = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_city-pec-off']);
                    title.append(cityPec);
                    cityPec.innerText = `${city} \ `;

                    let dataBase = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_database']);
                    dataBase.innerText = `[ ${answer[city]['database']['id']} | ${answer[city]['database']['type'].toLowerCase()} ${answer[city]['database']['name']} | ${answer[city]['database']['id_pec']} ]`;
                    title.append(dataBase);
                } else {
                    let cityPec = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_city-pec']);
                    title.append(cityPec);
                    cityPec.innerText = `${city} \ `;

                    let dataBase = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_database-off']);
                    dataBase.innerText = `[ В базе данных нет такой записи ]`;
                    title.append(dataBase);
                }
                
                
            let optionsBlock = createElement('div', 'cell-data__options-block');
            blockInfo.append(optionsBlock);

                let obj = answer[city]['kladr'];
                for (let k in obj) {
                    let option = createElement('div', 'cell-data__option"');

                    let input = createElement('input', 'cell-data__option-input');
                    input.setAttribute('type', 'radio');
                    input.setAttribute('name', obj[k]['name']);
                    input.setAttribute('value', obj[k]['id_kladr']);
                    input.setAttribute('id', obj[k]['id_kladr']);
                    if (answer[city]['database']) {
                        if (answer[city]['database']['id_kladr'] == answer[city]['kladr'][k]['id_kladr']) {
                            input.setAttribute('checked', true);
                        }
                    } else {
                        if (answer[city]['kladr'].length == 1) {
                            input.setAttribute('checked', true);
                        }
                    }
                    option.append(input);

                    let label = createElement('label', 'cell-data__option-label');
                    label.setAttribute('for', obj[k]['id_kladr']);
                    option.append(label);

                    label.innerText = `${obj[k]['typeShort']}. ${obj[k]['name']} ${obj[k]['regionName'] ? ", " + obj[k]['regionName'] + " " + obj[k]['regionTypeShort'].toLowerCase() : ""}`;
                    optionsBlock.append(option);
                }
                
                
        let blockButton = createElement('div', 'cell-data__block-button');
        cellData.append(blockButton);
                
            let buttonWrite = createElement('button', ['cell-data__button', 'cell-data__button_write']);
            buttonWrite.innerText = "Записать в БД";
            blockButton.append(buttonWrite);

            let buttonRewrite = createElement('button', ['cell-data__button', 'cell-data__button_rewrite']);
            buttonRewrite.innerText = "Перезаписать в БД";
            blockButton.append(buttonRewrite);

            let blockError = createElement('div', 'cell-data__block-error');
            blockButton.append(blockError);

        selectMonitor.append(cellData);
    }

    // создаём и вставляем кнопку "Записать все"
    buttonAll = createElement('button', 'cell-data__button');
    buttonAll.innerText = 'Записать все';
    selectMonitor.prepend(buttonAll);
    buttonAll.addEventListener('click', (e) => {
        for (let i = 0; i < buttonsWrite.length; i++) {
            recordMatch(buttonsWrite[i]);
        }
    })
    
}

// функция обработки клика по кнопке "Записать в БД
function recordMatch (button) {
    let cityInfo = button.parentElement.previousElementSibling.firstElementChild.firstElementChild,
        databaseInfo = button.parentElement.previousElementSibling.firstElementChild.children[1];
    let blockError = button.parentElement.querySelector('.cell-data__block-error');
    let options = button.parentElement.previousElementSibling.children[1].children;
    let dataForRecording = {}; // объект для сбора данных для записи в БД

    // проверяем есть ли отмеченные радио кнопки
    let numberRecord = null;
    for (let j = 0; j < options.length; j++) {
        if (options[j].firstElementChild.checked) {
            numberRecord = j;
        }
    }

    // функция для обработки ответа после записи в БД
    function afterRecordMatch (answer) {
        if (typeof(answer) == 'number') {
            if (answer === 0) {
                blockError.innerText = '';
                blockError.innerText = 'Такая запись уже есть в БД';
            } else if (answer > 0) {
                cityInfo.classList.remove('cell-data__subtitle_city-pec');
                cityInfo.classList.add('cell-data__subtitle_city-pec-off');
                databaseInfo.classList.remove('cell-data__subtitle_database-off');
                databaseInfo.classList.add('cell-data__subtitle_database');
                databaseInfo.innerText = '';
                databaseInfo.innerText = `[ ${answer} | ${arr[cityInfo.innerText]['kladr'][numberRecord]['type'].toLowerCase()} ${arr[cityInfo.innerText]['pec']['name_pec']} | ${arr[cityInfo.innerText]['pec']['id_pec']} ]`;
            }
        }
    }

    if (numberRecord != null) {
        blockError.innerText = '';
        dataForRecording = { // объект для сбора данных для записи в БД
            name: arr[cityInfo.innerText]['pec']['name_pec'],
            type: arr[cityInfo.innerText]['kladr'][numberRecord]['type'],
            id_kladr: arr[cityInfo.innerText]['kladr'][numberRecord]['id_kladr'],
            guid: arr[cityInfo.innerText]['kladr'][numberRecord]['guid'],
            id_pec: arr[cityInfo.innerText]['pec']['id_pec']
        };
        dataForRecording = JSON.stringify(dataForRecording);

        // Отправляем данные на запись в БД
        let ajax = new XMLHttpRequest();
        ajax.open('post', 'http://logist-master/admin/pec/add');
        ajax.onreadystatechange = function () {
            if (this.readyState == 4) { // запрос завершён
                if (this.status == 200) {
                    let res = JSON.parse(this.response);
                    afterRecordMatch(res);
                } else {
                    console.log(this.status, this.statusText);
                }
            }
        };
        ajax.send(dataForRecording);
    } else {
        blockError.innerText = '';
        blockError.innerText = 'Выберите значение для записи в БД';
    }
}

// функция добавляет события на все кнопки "Записать в БД"
function addEventToWriteDatabase () {
    // вешаем на все кнопки обработчик события клик
    for (let i = 0; i < buttonsWrite.length; i++) {
        buttonsWrite[i].addEventListener('click', (e) => {
            recordMatch(e.target);
        });
    }
}

button.addEventListener('click', (e) => {
    button.classList.add('red');
    let ajax = new XMLHttpRequest();
    ajax.open('get', 'http://logist-master/api/pec/cities');
    ajax.onreadystatechange = function () {
        if (this.readyState == 0) { // Исходное состояние
            console.log('Исходное состояние');
        } else if (this.readyState == 1) { // вызван метод open
            console.log('вызван метод open');
        } else if (this.readyState == 2) { // получены заголовки ответа
            console.log('получены заголовки ответа');
        } else if (this.readyState == 3) { // ответ в процессе передачи (данные частично получены)
            console.log("ответ в процессе передачи (данные частично получены)");
        } else if (this.readyState == 4) { // запрос завершён
            if (this.status == 200) {
                console.log(this.response);
                let res = JSON.parse(this.response);
                console.log(res);
                arr = res;
                outputInfo(monitor, res);
                buttonsWrite = document.querySelectorAll('.cell-data__button_write');
                addEventToWriteDatabase();
            } else {
                console.log(this.status, this.statusText);
            }
        } else {
            console.log('Идет загрузка');
        }
    },
    ajax.send();
})