<?php

  // Guards the store name chosen by store/last.php. A store is addressed like any other
  // type, so $padLastPush must not collide with a sequence type, a sequence tag, a sequence
  // option, an action or a PAD option - the type resolver in lib/type.php could no longer
  // tell them apart. Each collision is a padError.

  $pqStoreTaken = pqStoreTaken ( $padLastPush );

  if ( $pqStoreTaken )
    padError ( "Store name '$padLastPush' can not be equal to $pqStoreTaken" );

  // A name the page made something of already - an array, a {data}, {content}, {bool} or
  // {define} - is no free name either: the sequence store would take its place for {name}
  // (pqStoreHeld). The strict check says so, as the {data} side says it of a sequence store;
  // the lenient walk writes the store, as it did.

  elseif ( ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) and ( $pqStoreHeld = pqStoreHeld ( $padLastPush ) ) )
    padError ( "the store name '$padLastPush' is already $pqStoreHeld - {" . $padLastPush . "} would iterate the sequence in its place" );

?>
