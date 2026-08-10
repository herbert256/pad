<?php

  // Two tables over the same store, deliberately unrelated: the inner select has no
  // declared path to the outer one, and strict mode names the cross-product.

  $padSelect ['outerSel'] = [ 'db' => 'staff', 'key' => 'name' ];
  $padSelect ['innerSel'] = [ 'db' => 'staff', 'key' => 'name' ];

?>