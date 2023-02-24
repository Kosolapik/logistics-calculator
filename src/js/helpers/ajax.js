
// let params = {
//     method: 'post',
//     url: 'http://logist-master/api/calculate-delivery',
//     // data: {
//     //     "company": "pec",
//     //     "auto": {
//     //         "cost": 4190,
//     //         "time": [
//     //             "1",
//     //             "2"
//     //         ]
//     //     }
//     // },
//     json: '{"company":"kit","auto":{"cost":3404,"time":1}}',
// }

export class Ajax {
    constructor(params) {
        this.method = params['method'];
        this.url = new URL(params['url']);
        this.data = params['data'];
        this.json = params['json'];
        this.ajax = new XMLHttpRequest();

        /**
         * перебирает объект с данными и вставляем их в url как get параметры
         * @param {object} data ссылка на объект данных
         * @param {object} url ссылка на объект URL
         */
        function prepare_data(data, url) {
            for (let key in data) {
                if (typeof(data[key]) == 'object') {
                    prepare_data(data[key], url);
                } else {
                    url.searchParams.set(key, data[key]);
                }
            }
        }

        if (this.method == 'get') {
            if (this.data) {
                prepare_data(this.data, this.url);
            }
            this.ajax.open(this.method, this.url);
            this.ajax.send();
        }

        

        if (this.method == 'post') {
            this.ajax.open(this.method, this.url);
            if (this.json) {
                this.ajax.setRequestHeader('Content-type', 'application/json; charset=utf-8');
                if (typeof(this.json) != 'string') {
                    this.json = JSON.stringify(this.json);
                }
                this.ajax.send(this.json);
            } else {
                if (this.data) {
                    this.ajax.send(this.data);
                }
            }
        } 

        return this.ajax;
    };   
}



        


        