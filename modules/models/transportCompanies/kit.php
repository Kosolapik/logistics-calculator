<?php
namespace Models\TransportCompanies {
    class Kit extends \Models\TransportCompanies\TransportCompany {
        
        /**
         * Токен для авторизации
         * @var string
         */
        static private $token = '-0BzqLQVO9wzelbMTBhJOBzflTC6fukV';
        /**
         * url адрес api КИТа
         * @var string
         */
        static $baseUrl = 'https://capi.tk-kit.com';

        /**
         * выполняет запрос в КИТ
         * @param string $method метод запроса, обязательно вначале слеш
         * @param array $data параметры запроса
         * @return array возвращает массив с ответом КИТа
         */
        private function call($method, $data = []) {
            $url = self::$baseUrl . $method;
            $headerToken = 'Authorization: Bearer ' . self::$token;
            $curl = new \CurlWrapper([$headerToken]);
            $res = $curl->sendRequest('post', $url, $data);
            $res = json_decode($res, true);
            return $res;
        }

        /**
         * запрашивает полный список городов у КИТа
         * @return array возвращает "чистый" список городов России
         */
        function getCitiesAll() {
            $arrCities = $this->call('/1.0/tdd/city/get-list', [
                'country_code' => 'RU'
            ]);
            foreach ($arrCities as $i => $value) {
                if ($value['required_delivery'] == 1 && $value['required_pickup'] == 1 || $value['type'] == 'мкр') {
                    unset($arrCities[$i]);
                }
            }
            foreach ($arrCities as $i => &$value) {
                $reg = '#(?P<name>[^()]*)#ui';
                $reg_result = [];
                preg_match($reg, $value['name'], $reg_result); 
                $value['name'] = trim($reg_result['name']);
                $value['region'] = $value['region_code'];
                unset($value['region_code']);
                $value['country'] = $value['country_code'];
                unset($value['country_code']);

                if ($value['name'] == 'Южные ворота') {
                    unset($arrCities[$i]);
                }
                if ($value['name'] == 'Садовод') {
                    unset($arrCities[$i]);
                }
                if ($value['name'] == 'Мытищи') {
                    $value['region'] = 50;
                }
            }
            sort($arrCities);
            return $arrCities;
        }

        /**
         * метод фильтрует вложенные массивы "kladr"
         * 
         * метод получает масив городов КИТа, 
         * где в каждый город вложен массив "kladr", с вариантами нас.пунктов совпадающих по наименованию
         * метод отфильтрует все нас.пункты в массиве "kladr" которые не совпадают по региону с записью города
         * если такого массива в записе города не будет, ни чего не произойдёт
         * вернет тот же список городов с отфильтроваными вложеными массивами "kladr"
         * @param array $list массив городов КИТа
         * @return array
         */
        function filterArrayKladr(&$list) {
            foreach ($list as $i => &$value) {
                if ($value['kladr'] && count($value['kladr']) > 1) {
                    foreach ($value['kladr'] as $j => $info) {
                        if ($info['regionId_kladr'] && substr($info['regionId_kladr'], 0, 2) != $value['region']) {
                            unset($value['kladr'][$j]);
                        }
                    }
                    sort($value['kladr']);
                }
            }
            return $list;
        }

        /**
         * расчёт стоимости доставки
         * @param array $data массив данных из формы калькулятора
         * @return array возвращает json строку с резльтатом расчета доставки
         */
        function calculateDelivery($data) {
            $data = $this->prepareData($data, 'kit');
            extract($data);

            $params = [
                "city_pickup_code" =>  $fromLocalityId[0]['code'],
                "city_delivery_code" =>  $whereLocalityId[0]['code'],
                "declared_price" =>  $price,
                "confirmation_price" => true,
                "service" =>  [],
                "currency_code" =>  [
                    "RUB"
                ],
                "places" =>  [
                    [
                        "count_place" => $quantity,
                        // "height" =>  $height * 100,
                        // "width" =>  $width * 100,
                        // "length" =>  $length * 100,
                        "weight" =>  $weight,
                        "volume" => $volume,
                    ],
                ],
                "cargo_type_code" => $typeCargo,
                "insurance" =>  "1",
                "insurance_agent_code" =>  "8000152423"
            ];
            $res = $this->call('/1.0/order/calculate', $params);

            $arrCalc['company'] = 'kit';
            if (key_exists('validate', $res)) {
                $arrCalc['errors'] = $res['validate'];
            } else {
                $arrCalc['auto'] = [
                    'cost' => $res[0]['standart']['cost'],
                    'time' => $res[0]['standart']['time'],
                ];
            }
            return $arrCalc;
            // return $res;
        }
    }
}
?>