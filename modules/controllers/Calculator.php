<?php
    namespace Controllers {
        class Calculator extends Controller {
    
            function show() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $this->render('calculator', $_GET);
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->render('calculator', $_POST);
                }
            }
            
            function getHints() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $kladr = new \Models\Kladr();
                    $hints = $kladr->requestToKladr($_GET); // JSON дынные от kladr
                    echo $hints;
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $kladr = new \Models\Kladr();
                    $hints = $kladr->requestToKladr($_POST); // JSON дынные от kladr
                    echo $hints;
                }
            }

            function calculateDelivery() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $dataForm = file_get_contents('php://input');
                    $dataForm = json_decode($dataForm, true);

                    $pec = new \Models\Pec();
                    $culcPec = $pec->calculateDelivery($dataForm);
                    $arrCulc = [
                        'pec' => $culcPec
                    ];

                    // $kit = new \Models\Kit();
                    // $cityKit = $kit->getCitiesList();
                    // $arrCulc['kit'] = $cityKit;
                    
                    $arrCulc = json_encode($arrCulc, JSON_UNESCAPED_UNICODE);
                    echo $arrCulc;
                }
            }
        }
    }
?>