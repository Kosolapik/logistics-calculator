<?php
    namespace Models {
        class Pec {
            /*
            Метод возвращает array "грязный" список городов ПЭКа
            */
            function getListCities() {
                $curl = new \CurlWrapper();
                $cities = $curl->sendRequest('get', 'https://pecom.ru/ru/calc/towns.php'); // JSON "грязный" список городов ПЭКа
                $cities = json_decode($cities, true); // array "грязный" список городов
                return $cities;
            }


            /*
            Метод возвращает "чистый" список городов
            */
            function getCleanListCities() {
                $cities = self::getListCities(); // array "грязный" список городов ПЭКа
                function replaseName($name) { // Доп-функция для замены названий городов в списке. Возвращает новое значение.
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

                foreach ($cities as $city => $value) {
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
                    $city = replaseName($city); // и меняем название городов в некоторых ключях
                    $search = false; // переменная для 

                    // если в значение хранится массив (а там должен быть массив)
                    // запускаем новый цикл по вложенному массиву
                    if (gettype($value) == 'array') {
                        foreach ($value as $code => $locality) {
                            $locality = replaseName($locality); // меняем название городов в некоторых значениях
                            // очищаем значение элемента массива от лишней информации
                            $reg = '#(?P<name>[^,\.()]*\b)\s*(?P<info>[(,\.][^(]*(?=[(]))?\s*(?P<location>\(.*\))?#ui';
                            $reg_result = [];  // 
                            preg_match($reg, $locality, $reg_result);

                            if ($city == $reg_result['name']) {
                                $search = true;
                                $cleanListCities[] = [
                                    'name' => $city,
                                    'code' => $code,
                                    'type' => 'город'
                                ];
                                break;
                            }
                        }
                    }
                }
                return $cleanListCities;
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