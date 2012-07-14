<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->get_history('Email address');

echo "Result of GET /api/v3/subscribers/{list id}/history.{format}?email={email}\n<br />";
if($result->was_successful()) {
    echo "Got subscriber history <pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';