<?php
    namespace Models {
        class Pec {
            /*
                Метод возвращает JSON список городов ПЭКа
            */
            function getCities() {
                $curl = new \CurlWrapper();
                $cities = $curl->sendRequest('get', 'https://pecom.ru/ru/calc/towns.php'); // JSON список городов ПЭКа
                // $cities = json_encode($cities, JSON_UNESCAPED_UNICODE);
                return $cities;
            }

            /*
                Метод возвращает JSON с сопоставлеными городами
            */
            function compareCitiesPec() {
                /* 1.
                    Получаем от ПЭКа список городов в виде осоциативного массива, 
                    где ключ хранит название города, 
                    а значение  вложенный массив со списком самого города и рядом находящихся нас. пунктов
                */
                $cities_pec = self::getCities(); //JSON список городов ПЭКа
                $cities_pec = json_decode($cities_pec, true); // array список городов ПЭКа
                $kladr = new \Models\Kladr(); // создаём новый объект класса Кладр

                /*
                    Доп-функция для замены названий городов в списке городов от ПЭКа.
                    Возвращает новое значение.
                */
                function replaseName($name) {
                    if ($name == 'Владикавказ ФР') {
                        $name = 'Владикавказ';
                    } else if ($name == 'Москва Восток') {
                        $name = 'Москва';
                    } else if ($name == 'Орел') {
                        $name = 'Орёл';
                    } else if ($name == 'Наро-Фоминск Киевское шоссе') {
                        $name = 'Наро-Фоминск';
                    };
                    return $name;
                };

                /* 2.
                    -Преребераем полученный массив городов от ПЭКа.
                    Если элемент массива содержит вложенный массив (а он его должен содержать), перебераем вложенный масив.

                    -Во вложенном массиве перебираем значения с помощью рег. выр-ия, 
                    разделяем строку и находим: чистое название нас.пункта, доп инфа (если есть) и название района (если есть).
                    Сравниваем "чистое название" с названием города.

                    -Если мы нашли совпадение, мы нашли во вложенном массиве элемент хранящий в ключе ИД города в базе ПЭКа.
                    Мы поработаем с этим элементом, сделаем запрос в Кладр, сформируем новый масив с даными для вывода и остановим дальнейщий поиск совпадений по вложенному массиву, и перейдём к следующему элементу родительского массива.

                    -С совпавщим элементом делаем запрос в Кладр. Ищем населённые пункты которые в названии начинаются так же как наш город. 
                    В ответе от Кладр могут находиться несколько населёных пунктов с полностью совпадающими наименованиями или начинающимися так же как искомый нами город. 

                    -Перебераем результаты Кладр. Ищем поное совпадение в наименованиях нас. пунктов с названием города в ПЭКе и работаем с этими элементами.
                    Из каждого полученного элемента формируем новый масив данных. Каждый созданный массив добавляем как новый элемент массива, который будет содержать инфу о всех одноимённых населеных пунктах.
                    И этот массив одноимённых нас. пунктов записываем как значение нового элемента, под ключом kladr.

                    -Далее по ИД города в ПЭКе делаем запрос в БД, смотрим есть там запись с таким id_pec. 
                    Если есть, то добавляем в массив "город ИД" элемент "database" со значением записи из БД, в виде ассоциативного массива.

                    -Итоговый многомерный массив будет возвращён в качестве ответа.
                        [
                            "город ИД" => [
                                "kladr" => [
                                    "0" => [],
                                    "1" => [],
                                    ... => [],
                                    "n" => []
                                ],
                                "database" => [
                                    "id" => 667,
                                    "name" => "Абакан",
                                    ... => ...,
                                    "n" => ...
                                ]
                            ],

                            "город ИД" => [
                                "kladr" => [
                                    "0" => [],
                                    "1" => [],
                                    ... => [],
                                    "n" => []
                                ],
                                "database" => [
                                    "id" => 667,
                                    "name" => "Абакан",
                                    ... => ...,
                                    "n" => ...
                                ]
                            ],
                        ]
                */
                foreach ($cities_pec as $city => $value) {

                    // Функция для ограничения кол-ва итераций. Для режима разработок.
                    // $jjj++;
                    // if ($jjj > 10) {
                    //     break;
                    // } 
                    // пропускаем итерацию некоторых городов
                    if ($city == ('Алматы')) {
                        continue;
                    } else if ($city == 'Нур-Султан') {
                        continue;
                    }else if ($city == 'Астана') {
                        continue;
                    } else if ($city == 'Минск') {
                        continue;
                    } else if ($city == 'Витебск') {
                        continue;
                    };
                    // и меняем название некоторых городов
                    $city = replaseName($city);

                    $search = false;

                    if (gettype($value) == 'array') {
                        foreach ($value as $id_pec => $locality) {
                            $locality = replaseName($locality);
   
                            $reg = '#(?P<name>[^,\.()]*\b)\s*(?P<info>[(,\.][^(]*(?=[(]))?\s*(?P<location>\(.*\))?#ui';
                            $reg_result = [];  // 
                            preg_match($reg, $locality, $reg_result);
                            
                            if ($city == $reg_result['name']) {
                                $search = true;
                                
                                $kladr_params = [
                                    'query' => $reg_result['name'],
                                    'contentType' => 'city',
                                    'withParent' => 1,
                                    'limit' => 15,
                                    'typeCode' => (1|2),
                                ];
                                $kladr_answer = $kladr->requestToKladr($kladr_params); // JSON ответ от kladr
                                $kladr_answer = json_decode($kladr_answer, true); // асоциативный массив ответ от kladr
                                $kladr_result = $kladr_answer['result'];  // массив с результатами от кладр

                                $arr = [];

                                for ($i = 1; $i < count($kladr_result); $i++) {
                                    if ($kladr_result[$i]['name'] == $city) {
                                        $arr['kladr'][] = [
                                            'name' => $kladr_result[$i]['name'],
                                            'type' => $kladr_result[$i]['type'],
                                            'typeShort' => $kladr_result[$i]['typeShort'],
                                            'id_kladr' => $kladr_result[$i]['id'],
                                            'guid' => $kladr_result[$i]['guid'],
                                            'regionName' => $kladr_result[$i]['parents'][0]['name'],
                                            'regionType' => $kladr_result[$i]['parents'][0]['type'],
                                            'regionTypeShort' => $kladr_result[$i]['parents'][0]['typeShort'],
                                            'regionId_kladr' => $kladr_result[$i]['parents'][0]['id'],
                                            'region_guid' => $kladr_result[$i]['parents'][0]['guid'],
                                        ];
                                    }
                                }

                                $data_base = new \Models\DataBases\Localities();
                                $select_params = [
                                    'where' => 'id_pec = ' . $id_pec
                                ];
                                $data_base->select($select_params);
                                foreach($data_base as $record) {
                                    $arr['database'] = $record;
                                }

                                $arr['pec']['id_pec'] = $id_pec;
                                $arr['pec']['name_pec'] = $city;
                                
                                $cities[$city . ' ' . $id_pec] = $arr;
                                break;
                            };
                        };
                    };

                    if (!$search) {
                        $cities[$city] = "NULL";
                    }
                };
                $cities = json_encode($cities, JSON_UNESCAPED_UNICODE); // кодируем асоциативный массив $cities в JSON-формат
                return $cities;  
            }

            function calculateDelivery($data) {

                $localities = new \Models\DataBases\Localities();

                $cityTake = $localities->select([
                    'fields' => ['id_pec'],
                    'where' => 'guid = :guid',
                    'values' => [
                        'guid' => $data['fromLocality']['guid']
                    ]
                ]);
                $cityTake = $cityTake->fetch(\PDO::FETCH_ASSOC);
                // print_r($cityTake);

                $cityDelivery = $localities->select([
                    'fields' => ['id_pec'],
                    'where' => 'guid = :guid',
                    'values' => [
                        'guid' => $data['whereLocality']['guid']
                    ]
                ]);
                $cityDelivery = $cityDelivery->fetch(\PDO::FETCH_ASSOC);
                // print_r($cityDelivery);

                $arrForRequest = [
                    'places' => [
                        0 => [
                            round((pow(($data['volume']/$data['quantity']/(pow(($data['volume']/$data['quantity']), 1/3) * 2.25)), 1/2)), 2),
                            round((pow(($data['volume']/$data['quantity']), 1/3) * 2.25), 2),
                            round((pow(($data['volume']/$data['quantity']/(pow(($data['volume']/$data['quantity']), 1/3) * 2.25)), 1/2)), 2),
                            $data['volume'],
                            $data['weight'],
                            0
                        ],
                    ],
                    'take' => [
                        'town' => $cityTake['id_pec'], // ид города забора
                    ],
                    'deliver' => [
                        'town' => $cityDelivery['id_pec'] // ид города доставки
                    ],
                    // 'plombir' => 21, // количество пломб
                    'strah' => $data['price'], // величина страховки
                    // 'pal' => 0, // палеты (0 - не требуется)

                ];

                $url = $url_pec . http_build_query($arrForRequest);

                $curl = new \CurlWrapper();
                $answer = $curl->sendRequest('get', 'http://calc.pecom.ru/bitrix/components/pecom/calc/ajax.php', $arrForRequest);
                $answer = json_decode($answer, true);
                // $answer = json_decode($cityTake, true);
                return $answer;
            }
        }
    }
?>