<?php
    namespace Models {
        class Kit {
            static private $token = '0u-NmsFSISrfJ3xTk14jcOV9OHb0sVMi';
            static $baseUrl = 'https://capi.tk-kit.com';

            /*
                Метод ишет город по названию. Полученый масив от КИТа перебирается и удаляются не нужные нас. пункты.
                Удаляются одноименные нас. пункты с других регионов и проверяется полное совпадение по наименованию.
            */
            function searchCity($arr) {
                $url = self::$baseUrl . '/1.0/tdd/city/get-list';
                $headerToken = 'Authorization: Bearer ' . self::$token;
                $curl = new \CurlWrapper([$headerToken]);
                $params = [
                    'title' => $arr['name']
                    // 'region_code' => 59,
                    // 'required_delivery' => 0
                ];
                $answerKit = $curl->sendRequest('post', $url, $params);
                $answerKit = json_decode($answerKit, true);
                $regNum = substr($arr['id'], 0, 2);
                foreach ($answerKit as $key => $value) {
                    if ($value['region_code'] !== $regNum) {
                        unset($answerKit[$key]);
                    }
                    if ($value['type'] == 'село') {
                        $value['type'] = 'поселок';
                    }
                    $reg_type = '#^' . $value['type'] . '.*#ui';
                    $res_type = preg_match($reg_type, $arr['type']);
                    if ($res_type == false) {
                        unset($answerKit[$key]);
                    }
                    $reg = '#^' . $arr['name'] . '\b(?!-)#ui';
                    $reg_result = [];
                    $res = preg_match($reg, $value['name']);
                    if ($res == false) {
                        unset($answerKit[$key]);
                    }
                };
                return $answerKit;
            }




            /*
                Метод получает от КИТа полный список городов из 18 тысяч строк.
                И переберает его и удаляет города где нет филиалов кита.
                Возвращает чистый список городов где есть склады КИТа.
            */
            function getCleanListCities() {
                $url = self::$baseUrl . '/1.0/tdd/city/get-list';
                $headerToken = 'Authorization: Bearer ' . self::$token;
                $curl = new \CurlWrapper([$headerToken]);
                $params = [
                    'country_code' => 'RU'
                ];
                $answerKit = $curl->sendRequest('post', $url, $params);
                $answerKit = json_decode($answerKit, true);
                foreach ($answerKit as $key => $value) {
                    $reg = '#[^()]*#ui';
                    $reg_result = [];
                    $res = preg_match($reg, $value['name'], $reg_result);
                    $value['name'] = $reg_result[0];
                    if ($value['required_pickup'] == 0) {
                        $cleanListCities[] = [
                            'name' => trim($value['name']),
                            'code' => $value['code'],
                            'type' => $value['type']
                        ];
                    }
                    
                }
                return $cleanListCities;
            }
        }
    }
?>