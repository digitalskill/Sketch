<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->get_webhooks();

echo "Result of GET /api/v3/lists/{ID}/webhooks\n<br />";
if($result->was_successful()) {
    echo "Got list webhooks\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';