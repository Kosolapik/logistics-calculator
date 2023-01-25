<?php
    ini_set('max_execution_time', 300); // Установка лимита времени исполнения скрипта
    class CurlWrapper {

        public $headers = [];
    
        function __construct(array $headers = []) {
            $this->headers = $headers;
        }
    
        public function sendRequest(string $method, string $url = '', array $params = []) {
            if ($url === '' || $url === null || $url === false) {
                return 'Укажите URL-адрес запроса';
            } 
            if ($method == 'get') {
                $ch = curl_init($url . '?' . http_build_query($params));
            } else if ($method == 'post') {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            } else if ($method == 'json') {
                $ch = curl_init($url);
                $this->headers[] = 'Content-Type:application/json';
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_UNICODE));
            }
            if ($this->headers !== '' && $this->headers !== null && $this->headers !== false) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            }
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    }
?>