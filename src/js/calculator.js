
import { processing_route_fields } from './modules/calculator/processing_route_fields.js';
import { collector_data_form } from './helpers/collector_data_form.js';
import { create_element } from './helpers/create_element.js';
import { Ajax } from './helpers/ajax.js';

// объект для сбора данных с формы
let obj_data_form = {};
// оработка полей маршрута, вывод подсказок, обработка кликов на подсказках
processing_route_fields('.form__cell_from', obj_data_form);
processing_route_fields('.form__cell_where', obj_data_form);
// обработка остальных полей формы, сбор данных в объект
let form__blocks = document.querySelectorAll('.form__block');
collector_data_form(form__blocks[1], obj_data_form);
collector_data_form(form__blocks[2], obj_data_form);
collector_data_form(form__blocks[3], obj_data_form);



// расчёт доставки при клике на кнопку "Расчитать"
let monitor = document.querySelector('.calculator__screen');

let buttonCalc = document.querySelector('.form__submit');
buttonCalc.addEventListener('click', (e) => {
    e.preventDefault();
    monitor.innerText = '';
    calculate_delivery('pec');
    calculate_delivery('kit');
})

/**
 * расчитывает доставку в указанной ТК
 * 
 * добавляет в объект obj_data_form элемент с ключом "company" со значением указаным в параметре
 * по этому элементу на строне сервера будет происходить выбор нужных таблиц и классов для расчета 
 * @param {string} company транспортная компания
 */
function calculate_delivery (company) {
    obj_data_form['company'] = company;
    let ajax = new Ajax({
        method: 'post',
        url: 'http://logist-master/api/calculate-delivery',
        json: obj_data_form
    });
    ajax.onload = () => {
        console.log(ajax.response);
        console.log(JSON.parse(ajax.response));
        show_сalculate(JSON.parse(ajax.response));
    };
};

/**
 * выводит данные расчета на экран
 * @param {object} data данные которые нужно вывести
 */
function show_сalculate (data) {
    let company = create_element('div', ['company'], monitor, 'append'),
        img = create_element('img', ['company__img'], company, 'append'),
        cost = create_element('div', ['company__cost'], company, 'append'),
        time = create_element('div', ['company__time'], company, 'append'),
        link = create_element('a', ['company__link'], company, 'append'),
        button = create_element('div', ['button', 'company__button'], link, 'append');
    console.log(link);
    button.innerText = 'Перейти на сайт компании';
    link.setAttribute('href' , data['website']);
    link.setAttribute('target' , '_blank');
        

    if (!data['errors']) {
        img.setAttribute('src', `/resources/images/${data['company']}.jpg`);
        cost.innerText = `${data['auto']['cost']}₽`;
        if (typeof(data['auto']['time']) == 'object') {
            if (data['auto']['time'][0] != data['auto']['time'][1]) {
                time.innerText = `${data['auto']['time'][0]}-${data['auto']['time'][1]} д.`;
            } else {
                time.innerText = `${data['auto']['time'][0]} д.`;
            }
        } else {
            time.innerText = `${data['auto']['time']} д.`;
        }
    }
}