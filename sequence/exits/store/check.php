<?php

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