<?php
namespace Exceptions;

class ValidationException extends \Exception {
    // validation errors are client's fault (400 Bad Request)
}