<?php
    namespace Models {
        class Kladr {
            /*
                Метод для получения списка данных (города, регионы, улицы и т.д).
                В качестве параметра получает масив с параметрами запроса. 
                Возвращает данные полученые от kladr-api.ru в json формате. 
            */
            function requestToKladr(array $params) {
                $curl = new \CurlWrapper();
                $data = $curl->sendRequest('get', 'https://kladr-api.ru/api.php', $params); // json данные от kladr 
                $data = json_decode($data, true); // массив данных от kladr

                /*
                    Некоторые исправления в данных от kladr:
                    - меняем некоторые названия регионов
                    - в Ханты-Мансийском регионе меняем typeShort региона
                */
                $searchContext = $data['searchContext']['contentType'];
                $countArr = count($data['result']);
                if ($searchContext == 'region') {
                    for ($i = 1; $i < $countArr; $i++) {
                        if ($data['result'][$i]['name'] == 'Кемеровская область - Кузбасс') {
                            $data['result'][$i]['name'] = 'Кемеровская';
                        } else if ($data['result'][$i]['name'] == 'Ханты-Мансийский Автономный округ - Югра') {
                            $data['result'][$i]['name'] = 'Ханты-Мансийский';
                        // } else if ($data['result'][$i]['name'] == 'Саха /Якутия/') {
                        //     $data['result'][$i]['name'] = 'Саха';
                        } else if ($data['result'][$i]['name'] == 'Северная Осетия - Алания') {
                            $data['result'][$i]['name'] = 'Северная Осетия';
                        };
                    }
                } else if ($searchContext == 'city') {
                    for ($i = 1; $i < $countArr; $i++) {
                        if ($data['result'][$i]['parents'][0]['name'] == 'Кемеровская область - Кузбасс') {
                            $data['result'][$i]['parents'][0]['name'] = 'Кемеровская';
                        } else if ($data['result'][$i]['parents'][0]['name'] == 'Ханты-Мансийский Автономный округ - Югра') {
                            $data['result'][$i]['parents'][0]['name'] = 'Ханты-Мансийский';
                            $data['result'][$i]['parents'][0]['typeShort'] = 'авт. округ';
                        // } else if ($data['result'][$i]['parents'][0]['name'] == 'Саха /Якутия/') {
                        //     $data['result'][$i]['parents'][0]['name'] = 'Саха';
                        } else if ($data['result'][$i]['parents'][0]['name'] == 'Северная Осетия - Алания') {
                            $data['result'][$i]['parents'][0]['name'] = 'Северная Осетия';
                        };
                    }
                }
                $data = json_encode($data, JSON_UNESCAPED_UNICODE); // изменённые json данные от kladr
                return $data;
            }
        }
    }
?>