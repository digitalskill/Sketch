<?php

require_once '../../csrest_segments.php';

$wrap = new CS_REST_Segments('Segment ID', 'Your API Key');

$result = $wrap->clear_rules();

echo "Result of DELETE /api/v3/segments/{ID}/rules\n<br />";
if($result->was_successful()) {
    echo "Cleared with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}