<?php

  // An array bound into where= as $name is a list, as an array in a bare db() placeholder
  // is: name in ($names) is name in ('jim','bob'), where the array became the one literal
  // 'jim,bob' and matched nothing - and on SQLite an "Array to string conversion".

  $padSelect ['staffList'] = [ 'db' => 'staff' ];

  $names = [ 'jim', 'bob' ];

?>
