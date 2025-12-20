<?php

  if ( in_array ( 'optional', $padPrmParse ) )
    if ( padValidTag ($padWords [0]) )
      return include PAD . 'options/optional.php';

  if ( $padPairSet ) return padLevelNoPair   ();
  else               return padLevelNoSingle ();

?>