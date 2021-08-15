<?php

namespace Corbpie\YouTubeApiClass;

use Exception;

class ytApiException extends Exception
{
    public function errorMessage(): string
    {//Error message
        return "Error on line {$this->getLine()} in {$this->getFile()}. {$this->getMessage()}.";
    }
}