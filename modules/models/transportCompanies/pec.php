<?php
namespace Models\TransportCompanies {
    class Pec extends \Models\TransportCompanies\TransportCompany
    {
        /**
         * Имя пользователя
         * @var string
         */
        static private $login = 'sana.kosolapov';
        /**
         * Ключ доступа к API
         * @var string
         */
        static private $key = '546F0A6273DD3EE5B22A0276C5317A322BDDA693';
        /**
         * Базовый URL
         * @var string
         */
        static private $url = 'https://kabinet.pecom.ru/api/v1/';
        /**
         * Сайт ТК
         * @var string
         */
        static $website = 'https://pecom.ru/services-are/shipping-request/';

        /**
         * выполняет запрос в ПЭК
         * @param string $controller Название группы
         * @param string $action Название метода
         * @param array $data Входящие данные запроса
         * @return array Возвращает массив с ответом ПЭКа
         */
        private function call($controller, $action, $data = []) {
            $params = [
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST => TRUE,
                CURLOPT_SSL_VERIFYPEER => TRUE,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => sprintf('%s:%s', self::$login, self::$key),
            ];
            $curl = new \CurlWrapper();
            $res = $curl->sendRequest('json', self::$url . "/{$controller}/{$action}", $data, $params);
            $res = json_decode($res, true);
            return $res;
        }

        /**
         * запрашивает у ПЭКа полный массив городов
         * @return array возвращает массив с "чистым" списком городов России
         */
        function getCitiesAll() {
            $arr = $this->call('branches', 'all');
            $arr = $arr['branches'];
            foreach ($arr as $i => &$value) {
                if ($value['country'] != 'РОССИЯ') {
                    unset($arr[$i]);
                }

                unset($value['cities']);
                unset($value['divisions']);
                unset($value['timezone']);
                unset($value['id']);
                $reg = '#(республика\b\s)?(г.\s)?(\d*,*)?(?P<region>.*?(?=[.,]|обл|область|край|АО|респ))#ui';
                $reg_result = [];
                preg_match($reg, $value['address'], $reg_result); 
                $value['address'] = trim($reg_result['region']);

                if ($value['title'] == "Москва Восток") {
                    $value['title'] = "Москва";
                    $value['address'] = "Московская";
                } else if ($value['title'] == "Москва (ЦКАД)") {
                    unset($arr[$i]);
                } else if ($value['title'] == "Норильск Нефтебаза") {
                    $value['title'] = "Норильск";
                } else if ($value['title'] == "Орел") {
                    $value['title'] = "Орёл";
                } else if ($value['title'] == "Наро-Фоминск Киевское шоссе") {
                    $value['title'] = "Наро-Фоминск";
                    $value['address'] = "Московская";
                }

                if ($value['title'] == "Нижневартовск") {
                    $value['address'] = "Ханты-Мансийский";
                } else if ($value['title'] == "Санкт-Петербург") {
                    $value['address'] = "Ленинградская";
                } else if ($value['title'] == "Ухта") {
                    $value['address'] = "Коми";
                }

                if ($value['address'] == "Кабардино-Балкария") {
                    $value['address'] = "Кабардино-Балкарская";
                } else if ($value['address'] == "Нижний Новгород") {
                    $value['address'] = "Нижегородская";
                } else if ($value['address'] == "Северная Осетия – Алания") {
                    $value['address'] = "Северная Осетия";
                }

                $value['name'] = $value['title'];
                unset($value['title']);
                $value['region'] = $value['address'];
                unset($value['address']);
                $value['code'] = $value['bitrixId'];
                unset($value['bitrixId']);
                $value['postal_code'] = $value['postalCode'];
                unset($value['postalCode']);
                $value['acronym'] = $value['branchCode'];
                unset($value['branchCode']);
                $coordinates = $value['coordinates'];
                unset($value['coordinates']);
                $value['coordinates'] = $coordinates;
            }
            sort($arr);
            // array_multisort($arr, SORT_STRING);
            return $arr;
        }

        /**
         * метод фильтрует вложенные массивы "kladr"
         * 
         * метод получает масив городов ПЭКа, 
         * где в каждый город вложен массив "kladr", с вариантами нас.пунктов совпадающих по наименованию
         * метод отфильтрует все нас.пункты в массиве "kladr" которые не совпадают по региону с записью города
         * если такого массива в записе города не будет, ни чего не произойдёт
         * вернет тот же список городов с отфильтроваными вложеными массивами "kladr"
         * @param array $list массив городов ПЭКа
         * @return array 
         */
        function filterArrayKladr(&$list) {
            foreach ($list as $i => &$value) {
                if ($value['kladr'] && count($value['kladr']) > 1) {
                    foreach ($value['kladr'] as $j => $info) {
                        if ($info['regionName'] && $info['regionName'] != $value['region']) {
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
            $data = $this->prepareData($data, 'pec');
            extract($data);

            $params = [
                "senderCityId" =>  $fromLocalityId[0]['code'], // Код города отправителя [Number]
                "receiverCityId" =>  $whereLocalityId[0]['code'], // Код города получателя [Number]
                "calcDate" =>  "2023-01-28", // расчетная дата [Date]
                "isInsurance" =>  true, // Страхование [Boolean]
                "isInsurancePrice" =>  $price, // Оценочная стоимость, руб [Number]
                "isPickUp" =>  false, // Нужен забор [Boolean]
                "isDelivery" =>  false, // Нужна доставка [Boolean]
                "cargos" =>  [ // Данные о грузе/грузоместах груза (см.комментарии)[Array]
                    [
                        // "length" =>  $length, // Длина груза, м [Number]
                        // "width" =>  $width, // Ширина груза, м [Number]
                        // "height" =>  $height, // Высота груза, м [Number]
                        "volume" =>  $volume, // Объем груза, м3 [Number]
                        "maxSize" =>  $maxSize, // Максимальный габарит, м [Number]
                        "isHP" =>  false, // Защитная транспортировочная упаковка [Boolean]
                        "sealingPositionsCount" =>  0, // Количество мест для пломбировки - пломб (применяются для небольших мест или сложных грузов)[Number]
                        "weight" =>  $weight, // Вес, кг [Number]
                        "overSize" =>  false // Негабаритный груз [Boolean]
                    ]
                ]
            ];

            $res = $this->call('calculator', 'calculateprice', $params);
            $arrCalc['company'] = 'pec';
            $arrCalc['website'] = self::$website;
            if (key_exists('error', $res)) {
                $arrCalc['errors'] = $res['error'];
            } else {
                $arrCalc['auto'] = [
                    'cost' => $res['transfers'][0]['costTotal'],
                    'time' => $res['commonTerms'][0]['transporting'],
                ];
            }

            return $arrCalc;
        }
    }
}
?>