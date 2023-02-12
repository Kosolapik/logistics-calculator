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

            function compareCitiesPec() {
                $pec = new \Models\Pec();
                $cities = $pec->getCleanListCities(); // array "чистый" список городов
                $kladr = new \Models\Kladr();
                $cities = $kladr->compareCities($cities); // array сопоставленный с КЛАДР список городов
                $data_base = new \Models\DataBases\Localities();
                $cities = $data_base->compareCities($cities, 'id_pec'); // array сопоставленный с БД список городов
                $cities = json_encode($cities, JSON_UNESCAPED_UNICODE);
                echo $cities;
            }

            function compareCitiesKit() {
                $kit = new \Models\Kit();
                $cities = $kit->getCleanListCities(); // array "чистый" список городов
                $kladr = new \Models\Kladr();
                $cities = $kladr->compareCities($cities); // array сопоставленный с КЛАДР список городов
                $data_base = new \Models\DataBases\Localities();
                $cities = $data_base->compareCities($cities, 'id_kit'); // array сопоставленный с БД список городов
                $cities = json_encode($cities,  JSON_UNESCAPED_UNICODE);
                echo $cities;
            }

            function addPecRecord() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $data = file_get_contents('php://input');
                    $data = json_decode($data, true);
                    if (key_exists('id_pec', $data)) {
                        $key = 'id_pec';
                        $id_tc = $data['id_pec'];
                    } else if (key_exists('id_kit', $data)) {
                        $key = 'id_kit';
                        $id_tc = $data['id_kit'];
                    }
                    $data_base = new \Models\DataBases\Localities();
                    $select = $data_base->select([
                        'where' => 'id_kladr' . ' = :id_kladr',
                        'values' => [
                            'id_kladr' => $data['id_kladr']
                        ]
                    ])->fetch(\PDO::FETCH_ASSOC)
                        ? 
                            $data_base->select([
                                'where' => 'id_kladr' . ' = :id_kladr',
                                'values' => [
                                    'id_kladr' => $data['id_kladr']
                                ]
                            ])->fetch(\PDO::FETCH_ASSOC)
                        :
                            $data_base->select([
                                'where' => $key . ' = :' . $key,
                                'values' => [
                                    $key => $id_tc
                                ]
                            ])->fetch(\PDO::FETCH_ASSOC);
                    // var_dump($select);
                    // var_dump($data);
                    
                    if (!$select) {
                        $id = $data_base->insert($data);
                        echo ($id);
                    } else if ($select['id_kladr'] == $data['id_kladr'] and $select[$key] == $data[$key]) {
                        echo $select['id'];
                    } else if ($select['id_kladr'] == $data['id_kladr']) {
                        $data_base->update($data, $data['id_kladr'], 'id_kladr');
                        if ($data_base == true) {
                            echo $select['id'];
                        } else if ($data_base == false) {
                            echo 0;
                        }
                        // echo $data_base;
                    } else if ($select[$key] == $data[$key]) {
                        $data_base->update($data, $data[$key], $key);
                        // echo $data_base;
                        if ($data_base == true) {
                            echo $select['id'];
                        } else if ($data_base == false) {
                            echo 0;
                        }
                    }
                }
            }
        }
    }
?>