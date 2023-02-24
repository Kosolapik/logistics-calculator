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
                    
                    if ($dataForm['company'] == 'pec') {
                        $pec = new \Models\TransportCompanies\Pec();
                        $calcRes = $pec->calculateDelivery($dataForm);
                    } else if ($dataForm['company'] == 'kit') {
                        $kit = new \Models\TransportCompanies\Kit();
                        $calcRes = $kit->calculateDelivery($dataForm);
                    }
                    
                    $calcRes = json_encode($calcRes, JSON_UNESCAPED_UNICODE);
                    echo $calcRes;
                }
            }
        }
    }
?>