<?php
    namespace Models {
        require_once 'modules\curlWrapper.php';
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
                $cities_pec = self::getCities(); //JSON-данные список городов ПЭКа
                $cities_pec = json_decode($cities_pec, true); //массив список городов ПЭКа
                $kladr = new \Models\Kladr();

                function replaseName($name) { //  Функция для замены названия ключей в массиве
                    if ($name == 'Владикавказ ФР') {
                        $name = 'Владикавказ';
                    } else if ($name == 'Москва Восток') {
                        $name = 'Москва';
                    } else if ($name == 'Орел') {
                        $name = 'Орёл';
                    };
                    return $name;
                };

                foreach ($cities_pec as $region_pec => $value) {
                    if ($region_pec == ('Алматы')) {
                        continue;
                    } else if ($region_pec == 'Нур-Султан') {
                        continue;
                    } else if ($region_pec == 'Минск') {
                        continue;
                    };
                    $search = false;
                    $region_pec = replaseName($region_pec);
                    if (gettype($value) == 'array') {
                        foreach ($value as $id_pec => $locality_pec) {
                            $locality_pec = replaseName($locality_pec);
                            $rex = '#(?P<name>[^,\.()]*\b)\s*(?P<info>[(,\.][^(]*(?=[(]))?\s*(?P<location>\(.*\))?#ui';
                            $result_locality = [];
                            preg_match($rex, $locality_pec, $result_locality);
                            
                            if ($region_pec == $result_locality['name']) {
                                $search = true;

                                $kladr_params = [
                                    'query' => $result_locality['name'],
                                    'contentType' => 'city',
                                    'withParent' => 1,
                                    'limit' => 15,
                                    'typeCode' => (1|2),
                                    // 'districtId' => $result_locality['location']
                                ];
                                $kladr_answer = $kladr->requestToKladr($kladr_params); // JSON ответ от kladr
                                $kladr_answer = json_decode($kladr_answer, true); // асоциативный массив ответ от kladr
                                $result = $kladr_answer['result'];
                                for ($i = 1; $i < count($result); $i++) {
                                    if ($result[$i]['name'] == $region_pec) {
                                        $arr[$region_pec][] = [
                                            'region_pec' => $region_pec,
                                            'id_pec' => $id_pec,
                                            'name' => $result[$i]['name'],
                                            'type' => $result[$i]['type'],
                                            'typeShort' => $result[$i]['typeShort'],
                                            'id_kladr' => $result[$i]['id'],
                                            'guid' => $result[$i]['guid'],
                                            'regionName' => $result[$i]['parents'][0]['name'],
                                            'regionType' => $result[$i]['parents'][0]['type'],
                                            'regionTypeShort' => $result[$i]['parents'][0]['typeShort'],
                                            'regionId_kladr' => $result[$i]['parents'][0]['id'],
                                            'region_guid' => $result[$i]['parents'][0]['guid'],
                                        ];
                                    }
                                }
                                $cities[$region_pec . ' ' . $id_pec] = $arr[$region_pec];
                                break;
                            };
                        };
                    };
                    if (!$search) {
                        $cities[$region_pec] = "NULL";
                    }
                };
                $cities = json_encode($cities, JSON_UNESCAPED_UNICODE); // кодируем асоциативный массив $cities в JSON-формат
                return $cities;  
            }
        }
    }
?>