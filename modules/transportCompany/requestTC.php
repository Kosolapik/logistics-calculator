<?php
    namespace TransportCompany {

        class requestTC {
            
            public function prepareData($formData) {
            //    var_dump($formData);

               $dataPecom = [
                    "places" => [
                        0 => [
                            // 0 => "",
                            // 1 => "",
                            // 2 => "",
                            3 => $formData[volume],
                            4 => $formData[weight],
                            5 => 0,
                            6 => 0
                        ]
                    ],
                    "take" => [
                        "town" => $formData[from]
                    ],
                    "deliver" => [
                        "town" => $formData[where]
                    ]
               ];
            //    var_dump(http_build_query($dataPecom));

            $request = new \CurlWrapper();
            $params = [];
            $result = $request->sendRequest('get', 'https://pecom.ru/ru/calc/towns.php', $params);
            print_r($result);
            }
        }
    }
?>