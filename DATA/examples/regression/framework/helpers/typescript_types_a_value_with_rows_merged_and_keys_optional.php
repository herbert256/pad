<?php

  // The type of a value as pad types writes it: scalars by kind, a list of rows as one row
  // type - a key some rows lack optional, its types a union - other lists as a union of
  // their items, [] unknown[] and {} Record<string, unknown>, a key that is no identifier
  // quoted, and a page's name made a type name.

  $value = json_decode ( '{"id":7,"name":"Ada","paid":true,"note":null,"tags":["a","b"],"empty":[],"none":{},'
                       . '"rows":[{"a":1,"b":"x"},{"a":2.5,"c":true}],"mixed":[1,"two"],"odd key":1}' );

  $out = padTypeScriptInterface ( padTypeScriptName ( 'shop/order-list', 'Vars' ), $value );

?>
