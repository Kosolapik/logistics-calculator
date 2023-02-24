/**
 * собирает данные из формы в указанный объект
 * 
 * найдет в форме все input и повешает на них событие change
 * при срабатывание события в указанный объект будет добавляться или изменяться свойство объекта
 * формат свойства объекта: имя_поля = знач_поля
 * @param {string} select_form селектор формы
 * @param {object} seleect_obj ссылка на объект для хранения данных
 */
export function collector_data_form(select_form, seleect_obj) {
    let form;
    if (typeof(select_form) == 'string') {
        form = document.querySelector(select_form);
    } else {
        form = select_form;
    }
    
    let    inputs = form.querySelectorAll('input');
    for (let j = 0; j < inputs.length; j++) {
        inputs[j].addEventListener('change', (e) => {
            seleect_obj[e.target.name] = e.target.value;
            console.log(seleect_obj);
        })
    }
}
