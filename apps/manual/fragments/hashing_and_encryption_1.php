<?php

  $hash = padHash ( 'correct horse battery staple' );

  $right  = padHashCheck ( 'correct horse battery staple', $hash ) ? 'yes' : 'no';
  $wrong  = padHashCheck ( 'Tr0ub4dor&3', $hash ) ? 'yes' : 'no';
  $rehash = padHashNeedsRehash ( $hash ) ? 'yes' : 'no';
  $shape  = substr ( $hash, 0, 4 ) . '... ' . strlen ( $hash ) . ' characters';

?>
