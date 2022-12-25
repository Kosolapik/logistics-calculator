<?php
    namespace Controllers {
        require_once 'modules\curlWrapper.php';
        class Calculator  {
    
            function show() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    \Controllers\Controller::render('calculator', $_GET);
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    \Controllers\Controller::render('calculator', $_POST);
                }
            }
            function sendData() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $formData = $_GET;
                    $request = new \TransportCompany\requestPecom();
                    $request->prepareData($formData);
                    echo $request;
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $formData = $_POST;
                    $request = new \TransportCompany\requestTC();
                    $request->prepareData($formData);
                    echo $request;
                }
            }

            function query() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    $params = [
                        'query' => $_GET['query'],
                        'contentType' => $_GET['contentType'],
                        'withParent' => $_GET['withParent'],
                        'limit' => $_GET['limit'],
                        'regionId' => $_GET['regionId']
                    ];
                    
                    $headers = [
                       
                    ];
                    $req = new \CurlWrapper($headers);
                    $requer = $req->sendRequest('get', 'https://kladr-api.ru/api.php', $params);
                    $requer = json_encode($requer);
                    echo $requer;
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $res = $_POST['query'];
                    return json_encode($res);
                }
                // $query = new \CurlWrapper();
                // $res = $query->sendRequest('get', 'https://kladr-api.ru/api.php?', $_POST);
                    // print_r ($_SERVER);
                    
                    
                    // $result = json_encode($_POST, JSON_UNESCAPED_UNICODE);
                    // print_r ($result);
            }
        }
    }
?>