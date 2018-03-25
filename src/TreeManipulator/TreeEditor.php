<?php

namespace TreeManipulator;

use TreeManipulator\Interfaces\IteratorInterface;

/**
 * Class to edit tree structure
 */
class TreeEditor
{
    /**
     * @var IteratorInterface Iterator containing tree to edit
     */
    private $iterator;
    
    /**
     * TreeEditor constructor
     * 
     * @param IteratorInterface $iterator
     */
    public function __construct(IteratorInterface $iterator)
    {
        $this->iterator = $iterator;
    }
    
    /**
     * Iterator setter
     * 
     * @param IteratorInterface $iterator Iterator containing tree to edit
     */
    public function setIterator(IteratorInterface $iterator)
    {
        $this->iterator = $iterator;
    }
    
    /**
     * Iterator getter
     * 
     * @return IteratorInterface Iterator containing tree to edit
     */
    public function getIterator()
    {
        return $this->iterator;
    }
    
    /**
     * Return value from current position in interator
     * 
     * @return mixed Value in current position in iterator
     */
    public function readValue()
    {
        return $this->iterator->getCurrent();
    }
    
    /**
     * Return value from interator in path
     * Path start in current iterator position
     * 
     * @param string $path Path to element
     * @return mixed|null Value in path or null if not exitst
     */
    public function readValueFrom($path)
    {
        if (!$this->iterator->checkSimplePath($path)) {
            return null;
        }
        $this->iterator->performSimplePath($path);
        return $this->readValue();
    }
    
    /**
     * Write value to current position in interator
     * 
     * @param mixed $value Value to write
     */
    public function writeValue($value)
    {
        $this->iterator->setCurrent($value);
    }
    
    /**
     * Write value to interator in path
     * Path start in current iterator position
     * 
     * @param string $path Path to element
     * @param mixed $value Value to write
     * @param boolean $allowCreatePath If true, create nonexisten path
     * @return boolean True if value created
     */
    public function writeValueTo($path, $value, $allowCreatePath = true)
    {
        if ($this->iterator->checkSimplePath($path)) {
            $this->iterator->performSimplePath($path);
        } elseif ($allowCreatePath) {
            $this->createPath($path);
        } else {
            return false;
        }
        
        $this->writeValue($value);
        return true;
    }
    
    /**
     * Remove child from current iterator position
     * 
     * @param string $name Child to remove name
     */
    public function removeValue($name)
    {
        $currentPosition = $this->iterator->getCurrent();
        unset($currentPosition[$name]);
        $this->iterator->setCurrent($currentPosition);
    }
    
    /**
     * Remove child from path
     * 
     * @param string $path Path to element
     * @param string $name Child name
     * @return boolean True if value removed
     */
    public function removeValueFrom($path, $name)
    {
        if (!$this->iterator->checkSimplePath($path)) {
            return false;
        }
        $this->iterator->performSimplePath($path);
        $this->removeValue($name);
        return true;
    }
    
    /**
     * Add value in current iterator position
     * 
     * @param string $name Name of element
     * @param mixed $values Value of element
     */
    public function addValue($name, $values = null)
    {
        $currentPosition = $this->iterator->getCurrent();
        $currentPosition[$name] = $values;
        $this->iterator->setCurrent($currentPosition);
    }
    
    /**
     * Add value in path
     * 
     * @param string $path Path to parent element
     * @param string $name Name of element
     * @param mixed $value Value of element
     * @param boolean $allowCreatePath If true, create nonexisten path
     * @return boolean True if value added
     */
    public function addValueTo($path, $name, $value = null, $allowCreatePath = true)
    {
        if ($this->iterator->checkSimplePath($path)) {
            $this->iterator->performSimplePath($path);
        } elseif ($allowCreatePath) {
            $this->createPath($path);
        } else {
            return false;
        }
        
        $this->addValue($name, $value);
        return true;
    }
    
    /**
     * Add values in current iterator position
     * 
     * @param array $values Array of values to add
     */
    public function addValues(array $values)
    {
        $currentPosition = $this->iterator->getCurrent();
        foreach ($values as $key => $value) {
            $currentPosition[$key] = $value;
        }
        $this->iterator->setCurrent($currentPosition);
    }
    
    /**
     * Add values in path
     * 
     * @param string $path Path to element
     * @param array $values Array of values to create
     * @param boolean $allowCreatePath If true, create nonexisten path
     * @return boolean True if values added
     */
    public function addValuesTo($path, array $values, $allowCreatePath = true)
    {
        if ($this->iterator->checkSimplePath($path)) {
            $this->iterator->performSimplePath($path);
        } elseif ($allowCreatePath) {
            $this->createPath($path);
        } else {
            return false;
        }
        
        $this->addValues($values);
        return true;
    }
    
    /**
     * Create path if not exist
     * 
     * @param string $path Path to create
     */
    public function createPath($path)
    {
        if (!$this->iterator->checkSimplePath($path)) {
            $steps = explode(IteratorInterface::SEPARATOR, $path);
            foreach ($steps as $step) {
                if (!$this->iterator->hasChildNamed($step)) {
                    $this->addValue($step);
                }
                $this->iterator->performSimpleStep($step);
            }
            
        }
    }
    
    /**
     * Copy value from source path to destination path
     * 
     * @param string $srcPath Source path
     * @param string $descPath Destination path
     * @param boolean $allowCreatePath If true, create nonexisten path
     * @return boolean True if values added
     */
    public function copyValue($srcPath, $descPath, $allowCreatePath = true)
    {
        $tmpIterator = clone $this->iterator;
        if (!$tmpIterator->checkSimplePath($srcPath)) {
            return false;
        }
        $value = $tmpIterator->performSimplePath($srcPath);
        
        if ($this->iterator->checkSimplePath($descPath)) {
            $this->iterator->performSimplePath($descPath);
        } elseif ($allowCreatePath) {
            $this->createPath($descPath);
        } else {
            return false;
        }
        
        $this->writeValue($value);
        return true;
    }
}
