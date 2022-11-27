<?php
	
    require_once  'modules/models/curlWrapper.php';

    $params = [
        'auth' => '',
        'email' => [
            'updated_after' => ''
        ]
    ];
    
    $headers = [
        'Content-Type: application/json'
    ];
    $req = new CurlWrapper($headers);
    $requer = $req->sendRequest('post', 'https://api.tickets.yandex.net/api/agent', $params);

?>

<pre>
    <?php
        print_r($requer);
    ?>
</pre>