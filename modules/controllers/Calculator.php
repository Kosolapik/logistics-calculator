<?php
    namespace Controllers {
        class Calculator  {
    
            function show() {
                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    \Controllers\Controller::render('calculator', $_GET);
                } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    header('Content-Type:application/json');
                    
                    echo json_encode($_POST);
                }
            }
        }
    }
?>