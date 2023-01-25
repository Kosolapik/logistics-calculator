<?php
    namespace Controllers {
        require_once 'modules\curlWrapper.php';
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
        }
    }
?>