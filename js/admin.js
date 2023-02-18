"use strict";

let arr;
let buttonPec = document.querySelector('.button__pec');
let buttonKit = document.querySelector('.button__kit');
let monitor = document.querySelector('.showData');
let buttonAll;
let buttonsWrite;

/**
 * функция создает новый элемент и добавляет к нему классы
 * @param {string} tag тэг создаваемого элемента
 * @param {string|object} addedClass классы добавляемые к создаваемому элементу
 * @returns {object} возвращает ссылку на созданный обект
 */
function createElement(tag, addedClass, placeToAdd = null, selectWhere = null) {
    let element = document.createElement(tag);
    if (typeof(addedClass) == "string") {
        element.classList.add(addedClass);
    } else if (typeof(addedClass) == "object") {
        for (let i = 0; i < addedClass.length; i++) {
            let cl = addedClass[i];
            element.classList.add(cl);
        }
    }
    if (placeToAdd) {
        if (selectWhere == 'prepend') {
            placeToAdd.prepend(element);
        } else if (selectWhere == 'before') {
            placeToAdd.before(element);
        } else if (selectWhere == 'after') {
            placeToAdd.after(element);
        } else if (selectWhere == 'append') {
            placeToAdd.append(element);
        } else {
            placeToAdd.append(element);
        }
    }
    return element;
}


/**
 * функция выводит список городов транспортных компаний
 * @param {object} selectMonitor DOM-элемент для вывода
 * @param {object} list список городов
 */
function outputInfo(selectMonitor, list) {
    /* Очищаем экран вывода информации */
    selectMonitor.innerText = '';
    /*
        Запускаем цикл по пришедшему в параметрах объекту со списком городов.
        И для каждого элемента объекта (города) создаём ячеку для вывода информации.
        И заполняем её информацией хранящейся в значении этого элемента.
    */ 
    for (let number in list) {
        let cellData = createElement('div', 'cell-data', selectMonitor, 'append'); 
        let blockInfo = createElement('div', 'cell-data__block-info', cellData, 'append');
            let title = createElement('div', 'cell-data__title', blockInfo, 'append');
                let cityTC;
                let dataBase;
             
                cityTC = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_city'], title, 'append');
                cityTC.innerText = `${list[number]['name']} (${list[number]['region']}) [${list[number]['code']}]`;
                dataBase = createElement('div', ['cell-data__subtitle', 'cell-data__subtitle_database'], title, 'append');
                dataBase.innerText = `[ ${list[number]['db'][0]['id']} | ${list[number]['db'][0]['name']} | ${list[number]['db'][0]['code']} | ${list[number]['db'][0]['id_kladr']} | ${list[number]['db'][0]['region']} ]`;
                
                
                
            let optionsBlock = createElement('div', 'cell-data__options-block', blockInfo, 'append');
            let obj = list[number]['kladr'];
            for (let i in obj) {
                let option = createElement('div', 'cell-data__option', optionsBlock, 'append');

                let input = createElement('input', 'cell-data__option-input', option, 'append');
                input.setAttribute('type', 'radio');
                input.setAttribute('name', obj[i]['name']);
                input.setAttribute('value', obj[i]['id_kladr']);
                input.setAttribute('id', obj[i]['id_kladr']);
                if (list[number]['db'][0]['id_kladr'] == list[number]['kladr'][i]['id_kladr']) {
                    input.setAttribute('checked', true);
                } else if (list[number]['kladr'].length == 1) {
                    input.setAttribute('checked', true);
                };

                let label = createElement('label', 'cell-data__option-label', option, 'append');
                label.setAttribute('for', obj[i]['id_kladr']);

                label.innerText = `${obj[i]['typeShort']}. ${obj[i]['name']} ${obj[i]['regionName'] ? ", " + obj[i]['regionName'] + " " + obj[i]['regionTypeShort'].toLowerCase() : ""}`;
            }
                
        let blockButton = createElement('div', 'cell-data__block-button', cellData, 'append');
            let buttonWrite = createElement('button', ['cell-data__button', 'cell-data__button_write'], blockButton, 'append');
            buttonWrite.innerText = "Записать в БД";
            // let buttonRewrite = createElement('button', ['cell-data__button', 'cell-data__button_rewrite'], blockButton, 'append');
            // buttonRewrite.innerText = "Перезаписать в БД";
            let blockError = createElement('div', 'cell-data__block-error', blockButton, 'append');
    }

    // создаём и вставляем кнопку "Записать все"
    buttonAll = createElement('button', 'cell-data__button', selectMonitor, 'prepend');
    buttonAll.innerText = 'Записать все';
}

/**
 * функция обработки клика по кнопке "Записать в БД
 * @param {*} button 
 * @param {*} index 
 * @param {*} table 
 */
function recordMatch (button, index, table) {
    let cityInfo = button.parentElement.previousElementSibling.firstElementChild.firstElementChild,
        databaseInfo = button.parentElement.previousElementSibling.firstElementChild.children[1];
    let blockError = button.parentElement.querySelector('.cell-data__block-error');
    let options = button.parentElement.previousElementSibling.children[1].children;
    let dataForRecording = {}; // объект для сбора данных для записи в БД

    // проверяем есть ли отмеченные радио кнопки
    let numberOption = null;
    for (let j = 0; j < options.length; j++) {
        if (options[j].firstElementChild.checked) {
            numberOption = j;
        }
    }

    // функция для обработки ответа после записи в БД
    function afterRecordMatch (answer) {
        if (answer == 0) {
            blockError.innerText = '';
            blockError.innerText = 'Такая запись уже есть в БД';
        } else {
            blockError.innerText = '';
            blockError.innerText = 'Запись добавлена в БД';
            databaseInfo.innerText = '';
            databaseInfo.innerText = `[ ${answer[0]['id']} | ${answer[0]['name']} | ${answer[0]['code']} | ${answer[0]['id_kladr']} | ${answer[0]['region']} ]`;
        }
    }

    if (numberOption != null) {
        blockError.innerText = '';
        dataForRecording = { // объект для сбора данных для записи в БД
            'values': {
                'id_kladr': arr[index]['kladr'][numberOption]['id_kladr'],
            },
            'name': arr[index]['name'],
            'code': arr[index]['code'],
            'table': table
        };
        dataForRecording = JSON.stringify(dataForRecording);

        let url;
        if (table == 'pec_cities') {
            url = 'http://logist-master/api/pec/add_idkladr';
        } else if (table == 'kit_cities') {
            url = 'http://logist-master/api/kit/add_idkladr';
        }
        // Отправляем данные на запись в БД
        let ajax = new XMLHttpRequest();
        ajax.open('post', url);
        ajax.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // console.log(this.response);
                    let res = JSON.parse(this.response);
                    // let res = (this.response);
                    // console.log(res);
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
function addEventToWriteDatabase (table) {
    // вешаем на все кнопки обработчик события клик
    for (let i = 0; i < buttonsWrite.length; i++) {
        buttonsWrite[i].addEventListener('click', (e) => {
            recordMatch(e.target, i, table);
        });
    }
}
// функция вешает событие на кнопку "Записать все"
function addEventToWriteDatabaseAll (table) {
    buttonAll.addEventListener('click', (e) => {
        for (let i = 0; i < buttonsWrite.length; i++) {
            recordMatch(buttonsWrite[i], i, table);
        }
    })
}


buttonPec.addEventListener('click', (e) => {
    let ajax = new XMLHttpRequest();
    ajax.open('get', 'http://logist-master/api/pec/get_cities');
    ajax.onreadystatechange = function () {
        if (this.readyState == 4) { // запрос завершён
            if (this.status == 200) {
                console.log(this.response);
                let res = JSON.parse(this.response);
                console.log(res);
                arr = res;
                outputInfo(monitor, res);
                buttonsWrite = document.querySelectorAll('.cell-data__button_write');
                addEventToWriteDatabase('pec_cities');
                addEventToWriteDatabaseAll('pec_cities');
            } else {
                console.log(this.status, this.statusText);
            }
        }
    },
    ajax.send();
})
buttonKit.addEventListener('click', (e) => {
    let ajax = new XMLHttpRequest();
    ajax.open('get', 'http://logist-master/api/kit/get_cities');
    ajax.onreadystatechange = function () {
        if (this.readyState == 4) { // запрос завершён
            if (this.status == 200) {
                console.log(this.response);
                let res = JSON.parse(this.response);
                console.log(res);
                arr = res;
                outputInfo(monitor, res);
                buttonsWrite = document.querySelectorAll('.cell-data__button_write');
                addEventToWriteDatabase('kit_cities');
                addEventToWriteDatabaseAll('kit_cities');
            } else {
                console.log(this.status, this.statusText);
            }
        }
    },
    ajax.send();
})