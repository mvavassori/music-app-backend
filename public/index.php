<?php
// imports

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// sanitize the request uri
echo "requestUri: $requestUri\n";
echo "requestMethod: $requestMethod\n";