<?php
    $base_path = $_SERVER['DOCUMENT_ROOT'] . '\\';
    require_once $base_path . 'modules\settings.php';

    function autoloader(string $class_name) {
        global $base_path;
        require_once $base_path . 'modules\\' . $class_name . '.php';
    }
    spl_autoload_register('autoloader');

    require_once $base_path . 'modules\router.php';
?>