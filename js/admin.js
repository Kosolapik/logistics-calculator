"use strict";

let arr;
let buttonPec = document.querySelector('.button__pec');
let buttonKit = document.querySelector('.button__kit');
let monitor = document.querySelector('.showData');
let buttonAll;
let buttonsWrite;

    
function outputInfo(selectMonitor, list) {
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
        Запускаем цикл по пришедшему в ответе массиву со списком городов.
        И для каждого элемента массива (города) создаём ячеку для вывода информации.
        И заполняем её информацией хранящейся в значении этого элемента.
    */ 
    for (let number in list) {
       
        let cellData = createElement('div', 'cell-data'); 
        let blockInfo = createElement('div', 'cell-data__block-info');
        cellData.append(blockInfo);

            let title = createElement('div', 'cell-data__title');
            blockInfo.append(title);
                let cityTC;
                let dataBase
                if (list[number]['database']) {
                    cityTC = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_city-off']);
                    dataBase = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_database']);
                    dataBase.innerText = `[ ${list[number]['database']['id']} | ${list[number]['database']['type'].toLowerCase()} ${list[number]['database']['name']} | ${list[number]['database']['id_pec']} ]`;
                } else {
                    cityTC = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_city']);
                    dataBase = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_database-off']);
                    dataBase.innerText = `[ В базе данных нет такой записи ]`;
                }
                cityTC.innerText = `${list[number]['type'][0]}. ${list[number]['name']} [${list[number]['code']}]`;
                title.append(cityTC);
                title.append(dataBase);
                
                
            let optionsBlock = createElement('div', 'cell-data__options-block');
            blockInfo.append(optionsBlock);

                let obj = list[number]['kladr'];
                for (let i in obj) {
                    let option = createElement('div', 'cell-data__option"');

                    let input = createElement('input', 'cell-data__option-input');
                    input.setAttribute('type', 'radio');
                    input.setAttribute('name', obj[i]['name']);
                    input.setAttribute('value', obj[i]['id_kladr']);
                    input.setAttribute('id', obj[i]['id_kladr']);
                    if (list[number]['database']) {
                        if (list[number]['database']['id_kladr'] == list[number]['kladr'][i]['id_kladr']) {
                            input.setAttribute('checked', true);
                        }
                    } else {
                        if (list[number]['kladr'].length == 1) {
                            input.setAttribute('checked', true);
                        }
                    }
                    option.append(input);

                    let label = createElement('label', 'cell-data__option-label');
                    label.setAttribute('for', obj[i]['id_kladr']);
                    option.append(label);

                    label.innerText = `${obj[i]['typeShort']}. ${obj[i]['name']} ${obj[i]['regionName'] ? ", " + obj[i]['regionName'] + " " + obj[i]['regionTypeShort'].toLowerCase() : ""}`;
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
}

// функция обработки клика по кнопке "Записать в БД
function recordMatch (button, index, id_tc) {
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
                databaseInfo.innerText = `[ ${answer} | ${arr[index]['kladr'][numberRecord]['type'].toLowerCase()} ${arr[index]['name']} | ${arr[index]['code']} ]`;
            }
        }
    }

    if (numberRecord != null) {
        blockError.innerText = '';
        dataForRecording = { // объект для сбора данных для записи в БД
            name: arr[index]['name'],
            type: arr[index]['kladr'][numberRecord]['type'],
            id_kladr: arr[index]['kladr'][numberRecord]['id_kladr'],
            guid: arr[index]['kladr'][numberRecord]['guid'],
        };
        dataForRecording[id_tc] = arr[index]['code'];
        console.log(dataForRecording);
        dataForRecording = JSON.stringify(dataForRecording);

        // Отправляем данные на запись в БД
        let ajax = new XMLHttpRequest();
        ajax.open('post', 'http://logist-master/admin/pec/add');
        ajax.onreadystatechange = function () {
            if (this.readyState == 4) { // запрос завершён
                if (this.status == 200) {
                    console.log(this.response);
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
function addEventToWriteDatabase (company) {
    // вешаем на все кнопки обработчик события клик
    for (let i = 0; i < buttonsWrite.length; i++) {
        buttonsWrite[i].addEventListener('click', (e) => {
            recordMatch(e.target, i, company);
        });
    }
}
// функция вешает событие на кнопку "Записать все"
function addEventToWriteDatabaseAll (company) {
    buttonAll.addEventListener('click', (e) => {
        for (let i = 0; i < buttonsWrite.length; i++) {
            recordMatch(buttonsWrite[i], i, company);
        }
    })
}


buttonPec.addEventListener('click', (e) => {
    let ajax = new XMLHttpRequest();
    ajax.open('get', 'http://logist-master/api/pec/cities');
    ajax.onreadystatechange = function () {
        if (this.readyState == 4) { // запрос завершён
            if (this.status == 200) {
                console.log(this.response);
                let res = JSON.parse(this.response);
                console.log(res);
                arr = res;
                outputInfo(monitor, res);
                buttonsWrite = document.querySelectorAll('.cell-data__button_write');
                addEventToWriteDatabase('id_pec');
                addEventToWriteDatabaseAll('id_pec');
            } else {
                console.log(this.status, this.statusText);
            }
        }
    },
    ajax.send();
})
buttonKit.addEventListener('click', (e) => {
    let ajax = new XMLHttpRequest();
    ajax.open('get', 'http://logist-master/api/kit/cities');
    ajax.onreadystatechange = function () {
        if (this.readyState == 4) { // запрос завершён
            if (this.status == 200) {
                console.log(this.response);
                let res = JSON.parse(this.response);
                console.log(res);
                arr = res;
                outputInfo(monitor, res);
                buttonsWrite = document.querySelectorAll('.cell-data__button_write');
                addEventToWriteDatabase('id_kit');
                addEventToWriteDatabaseAll('id_kit');
            } else {
                console.log(this.status, this.statusText);
            }
        }
    },
    ajax.send();
})