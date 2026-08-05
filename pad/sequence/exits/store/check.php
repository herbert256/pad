<?php

  // Guards the store name chosen by store/last.php. A store is addressed like any other
  // type, so $padLastPush must not collide with a sequence type, a sequence tag, a sequence
  // option, an action or a PAD option - the type resolver in lib/type.php could no longer
  // tell them apart. Each collision is a padError.

  if ( pqSeq ( $padLastPush ) )
    padError ( "Store name '$padLastPush' can not be equal to a Sequence name" );

  if ( file_exists ( PQ . "start/tags/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to a Sequence tag" );

  if ( file_exists ( PQ . "options/types/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to a Sequence option name" );

  if ( pqAction ( $padLastPush ) )
    padError ( "Store name '$padLastPush' can not be equal to an Action name" );

  if ( file_exists ( PAD . "options/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to a PAD option name" );

?>