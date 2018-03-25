<?php

namespace TreeManipulator\Exception;

/**
 * Exception of perform step by iterator
 */
class IteratorMoveException extends \Exception
{
    const ERROR_INVALID_MOVE = 'Trying perform invalid move';
    const CODE_INVALID_MOVE = 21;
    
    const ERROR_INVALID_MOVE_UP = 'Trying go to nonexistent parent element';
    const CODE_INVALID_MOVE_UP = 22;
    
    const ERROR_INVALID_MOVE_DOWN = 'Trying go to nonexistent child element';
    const CODE_INVALID_MOVE_DOWN = 23;
    
    const ERROR_INVALID_PATH = 'Path guide to nonexistent element';
    const CODE_INVALID_PATH = 24;
    
    /**
     * IteratorMoveException constructor
     * @param string $message Error message
     * @param integer $code Error code
     * @param \Exception $previous Prevoius error
     */
    public function __construct($message = self::ERROR_INVALID_MOVE, $code = self::CODE_INVALID_MOVE, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
