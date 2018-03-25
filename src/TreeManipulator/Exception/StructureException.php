<?php

namespace TreeManipulator\Exception;

/**
 * Tree structure data exception
 */
class StructureException extends \Exception
{
    const ERROR_INVALID_STRUCTURE = 'Invalid tree structure';
    const CODE_INVALID_STRUCTURE = 1;
    
    /**
     * StructureException constructor
     * @param string $message Error message
     * @param integer $code Error code
     * @param \Exception $previous Prevoius error
     */
    public function __construct($message = self::ERROR_INVALID_STRUCTURE, $code = self::CODE_INVALID_STRUCTURE, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
