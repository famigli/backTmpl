<?php

namespace App\Exception;
use Exception;

class ValidationException extends Exception
{
    protected $violations;

    public function __construct($violations, Exception $previousException = null) {
        parent::__construct(self::getViolationsMessages($violations), null, $previousException);
        $this->violations = $violations;
    }

    public function getViolations(){
        return $this->violations;
    }

    protected static function getViolationsMessages($violations) {
        $message = "";
        foreach ($violations as $violation) {
            if ($message !== "") 
                $message .= PHP_EOL;
            $message = $violation->getMessage();
        }
        return $message;
    }

}