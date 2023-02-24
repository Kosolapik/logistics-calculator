/**
 * функция создает новый элемент и добавляет к нему классы
 * @param {string} tag тэг создаваемого элемента
 * @param {string|object} addedClass классы добавляемые к создаваемому элементу
 * @param {object} placeToAdd переменная хранящая объект в который вставляем новый эленмент
 * @param {string} selectWhere метод вставки
 * @returns {object} возвращает ссылку на созданный обект
 */
export function create_element(tag, addedClass, placeToAdd = null, selectWhere = null) {
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