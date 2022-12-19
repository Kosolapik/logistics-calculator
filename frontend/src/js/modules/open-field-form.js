export {openFieldForm};

function openFieldForm(selector, input) {
    let inp = document.getElementsByName(input);
    let lab = document.querySelector(selector);
    console.log(inp);
    console.log(lab);

        lab.addEventListener('click', function () {
            inp[0].classList.toggle('display_off');
            lab.classList.toggle('display_off');
        });
    
}