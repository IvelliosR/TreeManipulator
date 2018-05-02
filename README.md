# TreeManipulator
TreeManipulator is a PHP library that allows you to easily navigate through tree data structures and change their content. The library works on tree structures saved as associative array.
The library allows:
- move around the tree by following single steps
- move around the tree by entering the path
- check if the given path exists in the tree
- search for existing paths matching the pattern
- download tree elements
- create tree elements
- delete tree elements
- copy fragments of tree
## Requirements
- php version at least 5.3.0
- composer
## Installation
We attach the library to the project using a composer. The composer.json file must contain:
```json
{
	"require": {
        "ivi/tree-manipulator": "dev-master"
    },
    "minimum-stability": "dev"
}
```
Then execute the `composer install` command. After that, you can add an autoloader to files in the project's src folder
```php
require_once '/../vendor/autoload.php';
```
and import library classes via namespace `TreeManipulator`.
## Documentation
### Interfaces
#### IteratorInterface
Interface describing how to interact with the iterator. This interface contains constants describing the path elements:
- SEPARATOR- the `/` character separates the steps in the path
- STEP_ROOT - empty element, means move to the root of the tree
- STEP_CURRENT - the character `.` denoting to remain in the current element
- STEP_UP - string `..` denoting the transition to the parent element
- STEP_ANY - a `*` sign meaning a child with an arbitrary name
- STEP_MANY_ANY - string `**` denoting descendants with arbitrary names

Interace declares following methods:
- setStructure($structure, $steps = null) : void - load tree data to iterator
- getStructure($encode = true) : array - load tree data from iterator
- getCurrent() : array - get tree fragment from current iterator position
- setCurrent($data) : void - set data in current iterator position
- getSteps() : array - get list of steps from root to current iterator position
- hasParent() : boolean - return true if current element has parent, false otherwise
- getCurrentChildsName() : array - return list of all element childs
- hasChildNamed($childName) : boolean - return true if current element has childs with name $childName, false otherwise
- moveRoot() : void - move iterator pointer to root
- moveUp() : void - move iterator pointer to parrent element
- moveDown($childName) : void - move iterator pointer to child with name from $childName argument
- performSimpleStep($step) : void - perform step, set as parameter
- reset() : void - move iterator pointer to root
- performSimplePath($path = IteratorInterface::STEP_CURRENT) : array - move iterator pointer in tree according to path
- checkSimplePath($path = IteratorInterface::STEP_CURRENT) : boolean - check if from current element exist path set as parameter and return true if it's correct
### Classes
#### StepsIterator
StepsIterator stores the tree structure, information about the current position in the tree and allows you to change this position. It implements the IteratorInterface interface and implements all functions declared in it. This class is a wrappers that allows you to perform tree operations.
#### PathFinder
This class transforms not a simple path (that is, containing steps `*` or `**`) into a list of simple paths existing in the tree on which we perform operations. It contains two public methods:
- isSimplePath($path) : boolean - returns true if the path is simple, or false if not
- createSimplePaths($iterator, $path) - for the specified iterator (containing the tree) and the given path creates a list of simple paths
#### TreeEditor
This class allow edit the tree structure. As the constructor parameter, it adopts an object implementing the IteratorInterface interface through which it performs operations on the tree. It has the following public methods:
- setIterator(IteratorInterface $iterator) : void - set new iterator
- getIterator() : void - get current iterator
- readValue() : void - get value from current iterator pointer
- readValueFrom($path) : void - get value from given path (moves iterator pointer)
- writeValue($value) : void - save value to current iterator pointer
- writeValueTo($path, $value, $allowCreatePath = true) : void -  save value to given path (moves iterator pointer), if $allowCreatePath is true, then create the path if it does not exist
- removeValue($name) : void - remove child with given name from current iterator position
- removeValueFrom($path, $name) : void - remove child with given name from given path (moves iterator pointer)
- addValue($name, $values = null) : void - add a child with given name and content in current iterator position
- addValueTo($path, $name, $value = null, $allowCreatePath = true) : void - add a child with given name and content in given path (moves iterator pointer), if $allowCreatePath is true, then create the path if it does not exist
- addValues(array $values) : void - add children in current iterator position
- addValuesTo($path, array $values, $allowCreatePath = true) : void - add children in given path (moves iterator pointer), if $allowCreatePath is true, then create the path if it does not exist
- createPath($path) : void - creates the given path or its fragment (if it does not exist)
- copyValue($srcPath, $descPath, $allowCreatePath = true) : void - move content from $srcPath to $descPath, if $allowCreatePath is true, then create the path if it does not exist
### Exceptions Classes
#### StructureException
Throwing, when given tree structure is incorrect. Error number 1.
#### StepsException
Throwing, when given steps list is incorrect. Error number 11.
#### IteratorMoveException
Throwing, when perform move to non existence element in tree. Error numbers:
- error 21 - trying to perform incorrect move
- error 22 - trying to move to non non-existent parent element
- error 23 - trying to move to non non-existentchild element
- error 24 - attempt to go through a non-existent path
## Usages
### Adding StepsIterator class to .php file
```php
<?php
require_once '/../vendor/autoload.php';
use TreeManipulator\StepsIterator;

$iterator = new StepsIterator();
var_dump($iterator);
```
### Creating iterator and move through tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	// other fields ...
	'transactions' => [
		[
			'transaction_id' => 123,
			'transactionStatus' => 'PAID',
			'products' => [
				[
					'product_id' => 01234,
					'value' => 1.11
				],
				[
					'product_id' => 56789,
					'value' => 2.22,
				]
			]
		],
		[
			'transaction_id' => 456,
			'transactionStatus' => 'PAID',
			'products' => [
				[
					'product_id' => 111111,
					'value' => 3.33
				]
			]
		]
	]
];

// Create iterator and set tree as data structure
$iterator = new StepsIterator();
$iterator->setStructure($tree);

// Move iterator pointer to transactions list
$iterator->moveDown('transactions');

// Move iterator pointer to second transaction
$iterator->moveDown('1');

// Save transaction data in variable
$a = $iterator->getCurrent();

// Back to transactions list
$iterator->moveUp();

// Back to root
$iterator->reset();

// Path to first transaction second product price
$path = '/transactions/0/products/1/value';
// Check if this path exist in tree
$iterator->checkSimplePath($path);
// Perform this path
$iterator->performSimplePath($path);
```
### Checking if the path is simple
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\PathFinder;

// Create PathFinder object
$pathFinder = new PathFinder();

// Simple path (without * and ** steps)
$path = '/step1/step2/step3';
// Will return true
$pathFinder->isSimplePath($path);

// Non simple path (with *)
$path = '/step1/step2/*';
// Will return false
$pathFinder->isSimplePath($path);

// Non simple path (with **)
$path = '/step1/**/step3';
// Will return false
$pathFinder->isSimplePath($path);
```
### Create a list of paths based on a pattern
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\PathFinder;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	// other fields ...
	'transactions' => [
		[
			'transaction_id' => 123,
			'transactionStatus' => 'PAID',
			'products' => [
				[
					'product_id' => 01234,
					'value' => 1.11
				],
				[
					'product_id' => 56789,
					'value' => 2.22,
				]
			]
		],
		[
			'transaction_id' => 456,
			'transactionStatus' => 'PAID',
			'products' => [
				[
					'product_id' => 111111,
					'value' => 3.33
				]
			]
		],
		[
			'transaction_id' => 789,
			'transactionStatus' => 'UNPAID',
			'products' => [
				[
					'product_id' => 333333,
					'value' => 4.44
				]
			]
		]
	]
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);

$pathFinder = new PathFinder();

$path = 'transactions/*';
$foundPaths = $pathFinder->createSimplePaths($iterator, $path);
// Will return Array ( [0] => transactions/0 [1] => transactions/1 [2] => transactions/2 )
print_r($foundPaths);

$path = 'transactions/**/product_id';
$foundPaths = $pathFinder->createSimplePaths($iterator, $path);
// Will return Array ( [0] => transactions/0 [1] => transactions/1 [2] => transactions/2 ) Array ( [0] => transactions/0/products/0/product_id [1] => transactions/0/products/1/product_id [2] => transactions/1/products/0/product_id [3] => transactions/2/products/0/product_id ) 
print_r($foundPaths);
```
### Reading values from tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

// Will return full tree
$treeEditor->readValue();

$treeEditor->getIterator()->moveDown('phones');
// Will return list of phone numbers
$treeEditor->readValue();

// Will return 'New York'
$treeEditor->readValueFrom('/addresses/0/city');
```
### Changing values in tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

$treeEditor->getIterator()->moveDown('phones');
// Will change list of phone numbers to 'foo' string
$treeEditor->writeValue('foo');

// Will change 'New York' to 'Dallas'
$treeEditor->writeValueTo('/addresses/0/city', 'Dallas');
```
### Removing values from tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

$treeEditor->getIterator()->moveDown('phones');
// Will remove first phone number
$treeEditor->removeValue('0');

// Will remove 'city' field from tree
$treeEditor->removeValueFrom('/addresses/0', 'city');
```
### Adding elements to tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

$treeEditor->getIterator()->moveDown('phones');
// Will add new phone number
$treeEditor->addValue('2', '999999999');

// Will add new address
$treeEditor->addValueTo('/addresses', '1', [
	'city' => 'Dallas',
	'street' => 'Main street',
	'number' => 456
]);
```
### Creating paths in tree
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

// Will create new path /foo/bar in tree
$treeEditor->createPath('foo/bar');
```
### Copying fragments of tree 
```php
<?php

require_once '/../vendor/autoload.php';

use TreeManipulator\StepsIterator;
use TreeManipulator\TreeEditor;

// Create tree data structure as array
$tree = [
	'user_id' => 1,
	'firstname' => 'John',
	'lastname' => 'Smith',
	'dateOfBirth' => '1990-01-01',
	'addresses' => [
		[
			'city' => 'New York',
			'street' => 'Broadway',
			'number' => 123
		]
	],
	'phones' => [
		'123456789',
		'987654321'
	],
	'email' => 'john.smith@mail.com',
	'subscribe' => false
];

$iterator = new StepsIterator();
$iterator->setStructure($tree);
$treeEditor = new TreeEditor($iterator);

// Will copy first address as addresses list second element
$treeEditor->copyValue('/addresses/0', 'addresses/1');
