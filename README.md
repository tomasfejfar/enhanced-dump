# Enhanced Dump
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

Add this file to your php.ini as auto prepend file:

`auto_prepend_file = /some/path/fnc.php`

then restart your apache/php. 


## Usage:
`d($array)` - vardumps with location

`dd($array)` - vardumps with location and die 

`ds($array)` - vardumps as string with location

`dsd($array)` - vardumps as string with location and die

`dsql($zendDbSelect)` - vardumps as string into textarea with autoselect (useful for copying generated sql)

`dmem()` - dumps memory info
