<?php
$request_path = $_GET['route'];

if ($request_path && $request_path[-1] == '/') {
    $request_path = substr($request_path, 0, strlen($request_path) - 1);
}

if ($request_path == '') {
    $controller = new \Controllers\Calculator();
    $controller->show();
} else if ($request_path == 'api/hints') {
    $controller = new \Controllers\Calculator();
    $controller->getHints();
} else if ($request_path == 'admin') {
    $controller = new \Controllers\Admin();
    $controller->show();
} else if ($request_path == 'api/pec/get_cities') {
    $controller = new \Controllers\Admin();
    $controller->pecGetCitiesAll();
} else if ($request_path == 'api/pec/add_idkladr') {
    $controller = new \Controllers\Admin();
    $controller->addPecRecord();
} else if ($request_path == 'api/calculate-delivery') {
    $controller = new \Controllers\Calculator();
    $controller->calculateDelivery();
} else if ($request_path == 'api/kit/get_cities') {
    $controller = new \Controllers\Admin();
    $controller->kitGetCitiesAll();
} else if ($request_path == 'api/kit/add_idkladr') {
    $controller = new \Controllers\Admin();
    $controller->addKitRecord();
} /*else {
    throw new Page404Exception();
}*/
