export {outputHints};

/*
    Функция для вывода выпадающих подсказок с городами.
    1. Вешает события ввода тескта на поле.
    2. Если введено 3 и более символов, отправляет запрос на наш сервер.
    3. При получении ответа, выводит его в выпадающих подсказках.
    4. На все выведеные подсказки вешает событие клика.
    5. При клике на одну из подсказок вставляет её значение в поле ввода. И удаляет все подсказки.
*/

function outputHints(select) {
    let cell = document.querySelector(select);
    let input = cell.querySelector('.form__input');
    let hintsBlock = cell.querySelector('.form__hints');

    input.addEventListener('input', (e) => {
        // Если есть подсказки, удаляем их
        let hints = hintsBlock.querySelectorAll('.form__hints-element');
        if (hints.length > 0) {
            for (let i = 0; i < hints.length; i++) {
                hints[i].remove();
            }
        };

        // if (input.value.length > 2) {
        //     let objectQuery = {};
        //     objectQuery = {
        //         query: input.value,
        //         contentType: "region",
        //         withParent: 1,
        //         limit: 5
        //     };
        
        //     let params = '';
        //     for (let key in objectQuery) {
        //         if (params === '') {
        //             params = `${key}=${objectQuery[key]}`;
        //         } else {
        //             params += `&${key}=${objectQuery[key]}`;
        //         }
        //     }
        //     console.log(objectQuery);
        //     console.log(params);
        //     let ajax = new XMLHttpRequest();
        //     let url = 'http://logist-master/api/hints?' + params;
        //     ajax.open('get', url, false);
        //         ajax.addEventListener('readystatechange', function () {
        //             if (this.readyState == 4 && this.status == 200) {
        //                 // console.log(this.responseText);
        //                 console.log(this.status);
        //                 // let res = JSON.parse(this.responseText)['result'];
        //                 // for (let i = 1; i < res.length; i++) {
        //                 //     console.log(res[i]);
        //                 // }
        //             }
        //         });
        //     ajax.send();
        // }
    })   
}

function openFild(select) {
    let block = document.querySelector(select);
    let link = block.querySelector('.form__link');
    let fild = block.querySelector('.form__input');

    link.addEventListener('click', (e) => {
        link.classList.add('display-off');
        fild.classList.remove('display-off');
    });
}
openFild('.form__cell_from');


/*
    Функция для отправки формы без перезагрузки страницы, получение ответа и вывод ответа на страницу
*/
// function getDataForm(selectorForm) {
//     let form = document.querySelector(selectorForm);
//     let submit = form.querySelector('.form__submit');
//     function sentForm(e) {
//         e.preventDefault();
//         let formData = new FormData(form);
//         let ajax = new XMLHttpRequest();
//         ajax.open('post', 'http://logist-master/api/calculator', false);
//         ajax.addEventListener('readystatechange', function () {
//             if (this.readyState == 4 && this.status == 200) {
//                 // console.log(this.responseText);
//             }
//         });
//         ajax.send(formData);
//         let showData = document.querySelector('.showData');
//         let res = ajax.responseText;
//         // res = JSON.parse(res);
//         console.log((ajax));
//         console.log(res);
//         // console.log(formData);
//         showData.innerHTML = res;

//     }
//     submit.addEventListener('click', sentForm);
// }


