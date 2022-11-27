export {getDataForm};

function getDataForm(selectForm) {
    let form = document.querySelector(selectForm);
    let submit = form.querySelector('.form__submit');
    function sentForm(e) {
        e.preventDefault();
        let formData = new FormData(form);
        let ajax = new XMLHttpRequest();
        let res;


        ajax.open('get', '../../test.json', false);

        
        ajax.addEventListener('readystatechange', function () {
            if (this.readyState == 4 && this.status == 200) {
                // console.log(this.responseText);
            }
        });
        ajax.send(formData);
        res = JSON.parse(ajax.responseText);
        console.log(res);
        
    }
    submit.addEventListener('click', sentForm);
}