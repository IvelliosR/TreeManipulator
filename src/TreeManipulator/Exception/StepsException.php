<?php

namespace TreeManipulator\Exception;

/**
 * Iterator steps list exception
 */
class StepsException extends \Exception
{
    const ERROR_INVALID_LIST = 'Invalid steps list';
    const CODE_INVALID_LIST = 11;
    
    /**
     * StepsException constructor
     * @param string $message Error message
     * @param integer $code Error code
     * @param \Exception $previous Prevoius error
     */
    public function __construct($message = self::ERROR_INVALID_LIST, $code = self::CODE_INVALID_LIST, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}