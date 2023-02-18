<?php
    namespace Controllers {
        class Admin extends Controller {
    
            function show() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $this->render('admin', []);
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->render('admin', []);
                }
            }

            /**
             * метод:
             * 1. получает чистый список городов России
             * 2. записывает все города в БД
             * 3. сопоставляет запись города КИТа с БД, добавляет вложенный массив с записью из БД
             * 4. ищет одноимённые города в КЛАДР и добавляет вложенные массивы с результатми
             * 5. фильтрует результаты от КЛАДР
             * @return json возвращает json списк городов КИТа сопоставленный с БД и КЛАДР
             */
            function pecGetCitiesAll() {
                $pec = new \Models\Pec();
                $cities = $pec->getCitiesAll(); // array список городов
                $db = new \Models\DataBases\PecCities();
                $db->recordNewCities($cities, 'code');
                $cities = $db->compareCities($cities, 'code');
                $kladr = new \Models\Kladr();
                $cities = $kladr->compareCities($cities);
                $cities = $pec->filterArrayKladr($cities);
                $cities = $cities = json_encode($cities, JSON_UNESCAPED_UNICODE);
                echo $cities;
            }

            function kitGetCitiesAll() {
                $kit = new \Models\Kit();
                $cities = $kit->getCitiesAll();
                $db = new \Models\DataBases\KitCities();
                $db->recordNewCities($cities, 'code');
                $cities = $db->compareCities($cities, 'code');
                $kladr = new \Models\Kladr();
                $cities = $kladr->compareCities($cities);
                $cities = $kit->filterArrayKladr($cities);
                $cities = json_encode($cities, JSON_UNESCAPED_UNICODE);
                echo $cities;
            }

            function addPecRecord() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $data = file_get_contents('php://input');
                    $data = json_decode($data, true);
                    $db = new \Models\DataBases\PecCities();
                    $record = $db->update($data['values'], $data['code'], 'code');
                    if ($record != 0) {
                        $record = $db->getRecords('code', $data['code']);
                    }
                    $record = json_encode($record, JSON_UNESCAPED_UNICODE);
                    echo $record;
                }
            }
            function addKitRecord() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $data = file_get_contents('php://input');
                    $data = json_decode($data, true);
                    $db = new \Models\DataBases\KitCities();
                    $record = $db->update($data['values'], $data['code'], 'code');
                    if ($record != 0) {
                        $record = $db->getRecords('code', $data['code']);
                    }
                    $record = json_encode($record, JSON_UNESCAPED_UNICODE);
                    echo $record;
                }
            }
        }
    }
?>