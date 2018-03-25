<?php

namespace TreeManipulator\Interfaces;

use TreeManipulator\Exception\StructureException;
use TreeManipulator\Exception\StepsException;
use TreeManipulator\Exception\IteratorMoveException;

/**
 * Interface for tree data iterators
 */
interface IteratorInterface
{
    /**
     * Path elements separator
     */
    const SEPARATOR = '/';
    
    /**
     * Step to root element
     */
    const STEP_ROOT = '';
    /**
     * Step to current element
     */
    const STEP_CURRENT = '.';
    /**
     * Step to parent element
     */
    const STEP_UP = '..';
    /**
     * Step to any element
     */
    const STEP_ANY = '*';
    /**
     * Step to multiple any elements
     */
    const STEP_MANY_ANY = '**';
    
    /**
     * Set tree data to iterator structure variable. If steps parameter is not default, set steps list to steps array.
     * 
     * @param array $structure Tree structure of data
     * @param array $steps List of steps from root to current position. Default null
     * @throws StructureException if provided structure is not array
     * @throws StepsException if provided steps list is not array
     */
    public function setStructure($structure, $steps = null);
    
    /**
     * Get tree data from iterator structure variable.
     * 
     * @return array $structure Tree structure of data
     */
    public function getStructure();
    
    /**
     * Return data from current position in tree.
     * 
     * @return array Tree data fragment
     */
    public function getCurrent();
    
    /**
     * Put data from parameter to current position in tree.
     * 
     * @param array $data Data to set
     */
    public function setCurrent($data);
    
    /**
     * Return current list of steps from root in tree.
     * 
     * @return array List of steps
     */
    public function getSteps();
	
    /**
     * Chect has current element parent. Return true if current element has parent (currnet steps list is longer that 0)
     * 
     * @return boolean True if element has parent
     */
    public function hasParent();
    
    /**
     * Return children names list of current tree fragment.
     * 
     * @return array Childs names
     */
    public function getCurrentChildsName();
    
    /**
     * Return true if current element in tree has a children with name provide in parameter.
     * 
     * @param string $childName Name of child
     * @return boolean True if current element has child with specific name
     */
    public function hasChildNamed($childName);
    
    /**
     * Move current iterator position to root.
     */
    public function moveRoot();
    
    /**
     * Move current iterator position to parrent of current element.
     * 
     * @throws IteratorMoveException if element hasn't parrent
     */
    public function moveUp();
    
    /**
     * Move current iterator position to child, with specific name, of current element.
     * 
     * @param string $childName Name of child element
     * @throws IteratorMoveException if current element hasn't child with specific name
     */
    public function moveDown($childName);
    
    /**
     * Move iterator current position using specific step provide in parameter.
     * 
     * @param string $step Step to perform
     * @throws IteratorMoveException if iterator trying move to nonexistent element
     */
    public function performSimpleStep($step);
    
    /**
     * Move current iterator position in tree from current position to root.
     */
    public function reset();
    
    /**
     * Perform path (simple, without * and ** signs) from current position in tree structure.
     * 
     * @param string $path Path contain list of steps
     * @return array Data from current iterator position
     * @throws IteratorMoveException if path guide to nonexistent element
     */
    public function performSimplePath($path = self::STEP_CURRENT);
    
    /**
     * Check if path (simple, without * and ** signs) exist from current position in tree structure.
     * 
     * @param string $path Path contain list of steps
     * @return boolean True if path exist, false otherwise
     */
    public function checkSimplePath($path = self::STEP_CURRENT);
}
