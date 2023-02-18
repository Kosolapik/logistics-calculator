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
                if ($searchContext == 'region') {
                    foreach ($data['result'] as $i => $value) {
                        if ($data['result'][$i]['name'] == 'Кемеровская область - Кузбасс') {
                            $data['result'][$i]['name'] = 'Кемеровская';
                        } else if ($data['result'][$i]['name'] == 'Ханты-Мансийский Автономный округ - Югра') {
                            $data['result'][$i]['name'] = 'Ханты-Мансийский';
                        } else if ($data['result'][$i]['name'] == 'Саха /Якутия/') {
                            $data['result'][$i]['name'] = 'Саха';
                        } else if ($data['result'][$i]['name'] == 'Северная Осетия - Алания') {
                            $data['result'][$i]['name'] = 'Северная Осетия';
                        };
                    }
                } else if ($searchContext == 'city') {
                    foreach ($data['result'] as $i => $value) {
                        if ($data['result'][$i]['parents'][0]['name'] == 'Кемеровская область - Кузбасс') {
                            $data['result'][$i]['parents'][0]['name'] = 'Кемеровская';
                        } else if ($data['result'][$i]['parents'][0]['name'] == 'Ханты-Мансийский Автономный округ - Югра') {
                            $data['result'][$i]['parents'][0]['name'] = 'Ханты-Мансийский';
                            $data['result'][$i]['parents'][0]['typeShort'] = 'авт. округ';
                        } else if ($data['result'][$i]['parents'][0]['name'] == 'Саха /Якутия/') {
                            $data['result'][$i]['parents'][0]['name'] = 'Саха';
                        } else if ($data['result'][$i]['parents'][0]['name'] == 'Северная Осетия - Алания') {
                            $data['result'][$i]['parents'][0]['name'] = 'Северная Осетия';
                        };
                    }
                }
                $data = json_encode($data, JSON_UNESCAPED_UNICODE); // изменённые json данные от kladr
                return $data;
            }

            /*
                Метод получает "чистый" список городов.
                Ищет по наименованию варианты в КЛАДР и добавляет их во вложенный масив.
                Возвращает список городов с вариантами от КЛАДР.
            */
            function compareCities(&$list) {
                foreach ($list as $key => $value) {
                    // Функция для ограничения кол-ва итераций. Для режима разработок.
                    // $jjj++;
                    // if ($jjj > 20) {
                    //     break;
                    // } 
                    $kladr_params = [
                        'query' => $value['name'],
                        'contentType' => 'city',
                        'withParent' => 1,
                        // 'limit' => 50,
                        // 'typeCode' => (1|2),
                    ];
                    $kladr_answer = self::requestToKladr($kladr_params); // JSON ответ от kladr
                    $kladr_answer = json_decode($kladr_answer, true); // асоциативный массив ответ от kladr
                    $kladr_result = $kladr_answer['result'];  // массив с результатами от кладр
                    $arr = [];
                    // if($value['name'] == 'Горда') {
                    //     var_dump($kladr_result);
                    // }
                    foreach ($kladr_result as $i => $val) {
                        if ($val['name'] == $value['name']) {
                            $arr[] = [
                                'name' => $val['name'],
                                'type' => $val['type'],
                                'typeShort' => $val['typeShort'],
                                'id_kladr' => $val['id'],
                                'guid' => $val['guid'],
                                'regionName' => $val['parents'][0]['name'],
                                'regionType' => $val['parents'][0]['type'],
                                'regionTypeShort' => $val['parents'][0]['typeShort'],
                                'regionId_kladr' => $val['parents'][0]['id'],
                                'region_guid' => $val['parents'][0]['guid'],
                            ];
                        }
                    }
                    $list[$key]['kladr'] = $arr;
                }
                return $list;
            }
        }
    }
?>