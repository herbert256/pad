<?php

  // Guards the store name chosen by store/last.php. A store is addressed like any other
  // type, so $padLastPush must not collide with a sequence type, a sequence tag, a sequence
  // option, an action or a PAD option - the type resolver in lib/type.php could no longer
  // tell them apart. Each collision is a padError.

  $pqStoreTaken = pqStoreTaken ( $padLastPush );

  if ( $pqStoreTaken )
    padError ( "Store name '$padLastPush' can not be equal to $pqStoreTaken" );

?>
