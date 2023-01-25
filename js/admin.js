"use strict";

function compareCitiesPec() {
    let button = document.querySelector('.button__pec');
    let monitor = document.querySelector('.showData');
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
                    console.log(JSON.parse(this.response));
                    monitor.innerHTML = JSON.parse(this.response);
                } else {
                    console.log(this.status, this.statusText);
                }
            } else {
                console.log('Идет загрузка');
            }
        },
        ajax.send();
    })
}

compareCitiesPec();