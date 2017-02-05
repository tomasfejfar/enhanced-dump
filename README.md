# Enhanced Dump

[![Build Status](https://travis-ci.org/tomasfejfar/enhanced-dump.svg?branch=master)](https://travis-ci.org/tomasfejfar/enhanced-dump)
````
o----php in index.php line 3----o
array(2) {
  [0]=>
  string(5) "Lorem"
  ["x"]=>
  string(9) "Not ipsum"
}
o-------------------------------o
````

## Instalation

### Using as auto-prepend file

Add this file to your php.ini as auto prepend file:

`auto_prepend_file = /some/path/fnc.php`

then restart your apache/php. 

### Using composer

`composer require tomasfejfar/enhanced-dump --dev`

## Usage:
`d()` - vardumps with location

`dd()` - vardumps with location and die 

`ds()` - vardumps as string with location

`dsd()` - vardumps as string with location and die

`dsql($toString)` - vardumps as string into textarea with autoselect (useful for copying generated sql)

`dmem()` - dumps memory info

`dtable()` - dumps tabular data

`dxml()` - pretty prints any XML
