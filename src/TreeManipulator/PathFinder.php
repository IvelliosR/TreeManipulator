<?php

namespace TreeManipulator;

use TreeManipulator\Interfaces\IteratorInterface;

/**
 * Class transforming non simple path to simple paths list
 */
class PathFinder
{
    /**
     * Check is path simple. Return true if path in parameter is simple or false  if path contain * or ** step.
     * 
     * @param string $path Path to check
     * @return boolean True if path is simple
     */
    public static function isSimplePath($path)
    {
        $steps = explode(IteratorInterface::SEPARATOR, $path);
        foreach ($steps as $step) {
            if (($step == IteratorInterface::STEP_ANY) || ($step == IteratorInterface::STEP_MANY_ANY)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Return list of paths. These paths match to path from parameter and don't contains * or ** steps.
     * 
     * @param IteratorInterface $iterator Object of iterator implement IteratorInterface interface
     * @param string $path Path to element
     * @return array List of simple paths, matched to path set in $path argument
     */
    public static function createSimplePaths(IteratorInterface $iterator, $path)
    {
        $tmpIterator = clone($iterator);
        $preStarPath = self::excludePreStarPath($path);
        $tmpIterator->performSimplePath($preStarPath);
        
        $possiblePaths = self::generatePossiblePaths($tmpIterator);
        
        $regex = self::transformPathToRegex($path);
        
        $matchPaths = [];
        foreach ($possiblePaths as $possiblePath) {
            if (preg_match($regex, $possiblePath)) {
                $matchPaths[] = $possiblePath;
            }
        }
        
        return $matchPaths;
    }
    
    /**
     * Return path to first * or ** element
     * 
     * @param string $path Full path
     * @return string Path to firs * or ** step
     */
    private static function excludePreStarPath($path)
    {
        $steps = explode(IteratorInterface::SEPARATOR, $path);
        $preStarSteps = self::excludePreStarSteps($steps);
        return implode(IteratorInterface::SEPARATOR, $preStarSteps);
    }
    
    /**
     * Return steps to first * or ** element
     * 
     * @param array $steps All steps
     * @return array Steps to first * or ** step
     */
    private static function excludePreStarSteps(array $steps)
    {
        $preStarSteps = [];
        foreach ($steps as $step) {
            if ($step != IteratorInterface::STEP_ANY && $step != IteratorInterface::STEP_MANY_ANY) {
                array_push($preStarSteps, $step);
            } else {
                return $preStarSteps;
            }
        }
    }
    
    /**
     * Generate all paths from current element in iterator
     * 
     * @param IteratorInterface $iterator Iterator to generate paths
     * @return array List of all paths from current iterator position
     */
    private static function generatePossiblePaths(IteratorInterface $iterator)
    {
        $possiblePaths = [];
        $stepsLists = self::generateFullStepsLists($iterator);
        foreach ($stepsLists as $stepsList) {
            $possiblePaths[] = implode(IteratorInterface::SEPARATOR, $stepsList);
        }
        return $possiblePaths;
    }

    /**
     * Generate list of steps sets from current iterator position
     * 
     * @param IteratorInterface $iterator Iterator to generate steps
     * @param array $paths List of already created paths
     * @return array List of all created paths
     */
    private static function generateFullStepsLists(IteratorInterface $iterator, $paths = [])
    {
        $paths[] = $iterator->getSteps();
        $childs = $iterator->getCurrentChildsName();
        foreach ($childs as $child) {
            $tmpIterator = clone($iterator);
            $tmpIterator->moveDown($child);
            $paths = self::generateFullStepsLists($tmpIterator, $paths);
        }
        return $paths;
    }
    
    /**
     * Transform path to regex
     * 
     * @param string $path Path to transform
     * @return string Created regex
     */
    private static function transformPathToRegex($path)
    {
        $steps = explode(IteratorInterface::SEPARATOR, $path);
        foreach ($steps as $key => $step) {
            if ($step == IteratorInterface::STEP_ANY) {
                $steps[$key] = '[^\/]*';
            }
            if ($step == IteratorInterface::STEP_MANY_ANY) {
                $steps[$key] = '.*';
            }
        }
        $regex = implode('\\' . IteratorInterface::SEPARATOR, $steps);
        return '/^' . $regex . '$/';
    }
}
