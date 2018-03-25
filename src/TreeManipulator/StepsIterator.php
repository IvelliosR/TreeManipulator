<?php

namespace TreeManipulator;

use TreeManipulator\Interfaces\IteratorInterface;
use TreeManipulator\Exception\StructureException;
use TreeManipulator\Exception\StepsException;
use TreeManipulator\Exception\IteratorMoveException;

/**
 * Class to iterate data step by step
 */
class StepsIterator implements IteratorInterface
{
    /**
     * @var array Tree data structure
     */
    protected $structure;
    
    /**
     * @var mixed Current position in tree data structure
     */
    protected $currentPosition;
    
    /**
     * @var array List of steps from root to current position
     */
    protected $steps;
    
    /**
     * Perform steps, from root, using current steps list. Set current element value to current position of iterator.
     */
    private function moveCurrentPositionPointer()
    {
        $this->currentPosition = &$this->structure;
        foreach ($this->steps as $step) {
            $this->currentPosition = &$this->currentPosition[$step];
        }
    }
    
    /**
     * Set tree data to iterator structure variable. If steps parameter is not default, set steps list to steps array.
     * 
     * @param array $structure Tree structure of data
     * @param array $steps List of steps from root to current position. Default null
     * @throws StructureException if provided structure is not array
     * @throws StepsException if provided steps list is not array
     */
    public function setStructure($structure, $steps = null)
    {
        if (!is_array($structure)) {
            throw new StructureException();
        }
        $this->structure = $structure;
        
        if ($steps == null) {
            $this->steps = [];
        } else {
            if (!is_array($steps)) {
                throw new StepsException();
            }
            $this->steps = $steps;
        }
        
        $this->moveCurrentPositionPointer();
    }
    
    /**
     * Get tree data from iterator structure variable.
     * 
     * @return array $structure Tree structure of data
     */
    public function getStructure($encode = true)
    {
        if ($encode) {
            return json_decode(json_encode($this->structure), true);
        }
        return $this->structure;
    }
    
    /**
     * Return data from current position in tree.
     * 
     * @return array Tree data fragment
     */
    public function getCurrent()
    {
        return $this->currentPosition;
    }
    
    /**
     * Put data from parameter to current position in tree.
     * 
     * @param array $data Data to set
     */
    public function setCurrent($data)
    {
        $this->currentPosition = $data;
    }
    
    /**
     * Return current list of steps from root in tree.
     * 
     * @return array List of steps
     */
    public function getSteps()
    {
        return $this->steps;
    }
    
    /**
     * Chect has current element parent. Return true if current element has parent (currnet steps list is longer that 0)
     * 
     * @return boolean True if element has parent
     */
    public function hasParent()
    {
        if (count($this->steps) > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Return children names list of current tree fragment.
     * 
     * @return array Childs names
     */
    public function getCurrentChildsName()
    {
        if (is_array($this->currentPosition)) {
            $childs = [];
            foreach ($this->currentPosition as $key => $child) {
                array_push($childs, $key);
            }
            return $childs;
        }
        return [];
    }
    
    /**
     * Return true if current element in tree has a children with name provide in parameter.
     * 
     * @param string $childName Name of child
     * @return boolean True if current element has child with specific name
     */
    public function hasChildNamed($childName)
    {
        if (is_array($this->currentPosition) && array_key_exists($childName, $this->currentPosition)) {
            return true;
        }
        return false;
    }
    
    /**
     * Move current iterator position to root.
     */
    public function moveRoot()
    {
        $this->steps = [];
        $this->moveCurrentPositionPointer();
    }
    
    /**
     * Move current iterator position to parrent of current element.
     * 
     * @throws IteratorMoveException if element hasn't parrent
     */
    public function moveUp()
    {
        if ($this->hasParent()) {
            array_pop($this->steps);
            $this->moveCurrentPositionPointer();
        } else {
            throw new IteratorMoveException(IteratorMoveException::ERROR_INVALID_MOVE_UP, IteratorMoveException::CODE_INVALID_MOVE_UP);
        }
    }
    
    /**
     * Move current iterator position to child, with specific name, of current element.
     * 
     * @param string $childName Name of child element
     * @throws IteratorMoveException if current element hasn't child with specific name
     */
    public function moveDown($childName)
    {
        if ($this->hasChildNamed($childName)) {
            array_push($this->steps, $childName);
            $this->moveCurrentPositionPointer();
        } else {
            throw new IteratorMoveException(IteratorMoveException::ERROR_INVALID_MOVE_DOWN . ': ' . $childName, IteratorMoveException::CODE_INVALID_MOVE_DOWN);
        }
    }
    
    /**
     * Move iterator current position using specific step provide in parameter.
     * 
     * @param string $step Step to perform
     * @throws IteratorMoveException if iterator trying move to nonexistent element
     */
    public function performSimpleStep($step)
    {
        switch ($step) {
            case IteratorInterface::STEP_ROOT:
                $this->moveRoot();
                break;
            case IteratorInterface::STEP_CURRENT:
                break;
            case IteratorInterface::STEP_UP:
                $this->moveUp();
                break;
            default:
                $this->moveDown($step);
        }
    }

    /**
     * Move current iterator position in tree from current position to root.
     */
    public function reset()
    {
        $this->moveRoot();
    }
    
    /**
     * Perform path (simple, without * and ** signs) from current position in tree structure.
     * 
     * @param string $path Path contain list of steps
     * @return array Data from current iterator position
     * @throws IteratorMoveException if path guide to nonexistent element
     */
    public function performSimplePath($path = IteratorInterface::STEP_CURRENT)
    {
        $currentSteps = $this->getSteps();
        $this->moveCurrentPositionPointer();
        
        $steps = explode(IteratorInterface::SEPARATOR, $path);
        foreach ($steps as $step) {
            try {
                $this->performSimpleStep($step);
            } catch (IteratorMoveException $ex) {
                $this->steps = $currentSteps;
                $this->moveCurrentPositionPointer();
                throw new IteratorMoveException(IteratorMoveException::ERROR_INVALID_PATH, IteratorMoveException::CODE_INVALID_PATH, $ex);
            }
        }
        return $this->getCurrent();
    }
    
    /**
     * Check if path (simple, without * and ** signs) exist from current position in tree structure.
     * 
     * @param string $path Path contain list of steps
     * @return boolean True if path exist, false otherwise
     */
    public function checkSimplePath($path = self::STEP_CURRENT)
    {
        $currentSteps = $this->getSteps();
        $this->moveCurrentPositionPointer();
        
        $steps = explode(IteratorInterface::SEPARATOR, $path);
        foreach ($steps as $step) {
            try {
                $this->performSimpleStep($step);
            } catch (\Exception $ex) {
                $this->steps = $currentSteps;
                $this->moveCurrentPositionPointer();
                return false;
            }
        }
        
        $this->steps = $currentSteps;
        $this->moveCurrentPositionPointer();
        return true;
    }
}
