<?php
$request_path = $_GET['route'];

if ($request_path && $request_path[-1] == '/') {
    $request_path = substr($request_path, 0, strlen($request_path) - 1);
}

if ($request_path == '') {
    $controller = new \Controllers\Calculator();
    $controller->show();
} else if ($request_path == 'api/calculator') {
    $controller = new \Controllers\Calculator();
    $controller->sendData();
} else if ($request_path == 'api/hints') {
    $controller = new \Controllers\Calculator();
    $controller->query();
} /*else {
    throw new Page404Exception();
}*/
