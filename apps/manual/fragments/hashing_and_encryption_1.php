<?php

  $phrase = 'correct horse battery staple';
  $hash   = padHash ( $phrase );

  $right  = padHashCheck ( $phrase, $hash ) ? 'yes' : 'no';
  $wrong  = padHashCheck ( 'Tr0ub4dor&3', $hash ) ? 'yes' : 'no';
  $rehash = padHashNeedsRehash ( $hash ) ? 'yes' : 'no';
  $shape  = substr ( $hash, 0, 4 ) . '... '
          . strlen ( $hash ) . ' characters';

?>
