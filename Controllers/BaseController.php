<?php
namespace Controllers;

class BaseController {
    // send json response with status code
    protected function sendResponse(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function sendError(string $message, int $statusCode = 400) {
        return $this->sendResponse(["error" => $message], $statusCode);
    }

    // get json input from request body
    protected function getJsonInput(): array {
        // file_get_contents reads the entire content of the specified "file" (in this case, the php://input stream) into a string. So, $input will contain the raw JSON string sent by the client, i.e. the raw body.
        $input = file_get_contents('php://input'); // in practice is just a way to get the request body
        $data = json_decode($input, true);  // convert json raw string into php associative array

        if (json_last_error() !== JSON_ERROR_NONE) { // if the parsing went wrong
            $this->sendError('Invalid JSON input', 400);
        }

        return $data ?? [];
    }
}