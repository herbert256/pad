<?php

  // padRequest reads the input by name or dot path: a posted value over a query value of
  // the same name, every text trimmed as a request value is, a * mapping over a list, and
  // the default - a Closure called - for a name the request did not bring. The query key
  // that names the page is no input. The input here is set as if the request had sent it.

  $_GET  ['user']  = [ 'name' => '  Ann  ', 'tags' => [ ' red ', 'blue' ] ];
  $_GET  ['items'] = [ [ 'id' => 1, 'name' => ' pen ' ], [ 'id' => 2 ], [ 'id' => 3, 'name' => 'ink' ] ];
  $_GET  ['sort']  = ' date ';
  $_POST ['user']  = [ 'name' => ' Bob ' ];
  $_POST ['zero']  = '0';

  $r1 = padRequest ( 'user.name' );
  $r2 = padRequest ( 'sort' );
  $r3 = json_encode ( padRequest ( 'items.*.name' ) );
  $r4 = padRequest ( 'items.1.id' );
  $r5 = padRequest ( 'items.9.id', 'none' );
  $r6 = padRequest ( 'nope', fn () => 'called' );
  $r7 = padRequest ( $padPageKey, 'the page key is no input' );
  $r8 = padRequest ( 'zero', 'x' );
  $r9 = json_encode ( padRequestExcept ( 'padInclude' ) );

?>
