<?php
    namespace Models {
        class Kit {
            static private $token = '-0BzqLQVO9wzelbMTBhJOBzflTC6fukV';
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
        }
    }
?>