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
                $cities = $pec->compareCitiesPec(); // JSON список сопоставленных городов
                echo $cities;

                // /* PUT data comes in on the stdin stream */
                // $putdata = fopen("php://input", "r");
                // /* Open a file for writing */
                // $fp = fopen("php://output", "w+");
                // /* Read the data 1 KB at a time
                // and write to the file */
                // while ($data = fread($putdata)) {
                //     fwrite($fp, $data);
                // };
                // /* Close the streams */
                // fclose($fp);
                // fclose($putdata);
            }

            function addPecRecord() {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $data = file_get_contents('php://input');
                    $data = json_decode($data, true);
                    $data_base = new \Models\DataBases\Localities();
                    $id = $data_base->insert($data);
                    echo ($id);
                }
            }
        }
    }
?>