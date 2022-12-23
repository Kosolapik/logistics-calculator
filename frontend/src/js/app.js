// import {outputHints} from './modules/calculator-form.js';

// outputHints('.form__cell_from');
// // getDataForm('.form-calculator');


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