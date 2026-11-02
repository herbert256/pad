<?php

  // {pad:fragment 'order-list'} ... {/pad:fragment}: a named part of the page. It renders in place;
  // a request that names it - &padFragment=order-list - gets it alone, which level/end.php
  // sees to when the fragment's level closes (lib/respond.php).

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {pad:fragment} never closes" );

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( "the {pad:fragment} needs a name - {pad:fragment 'order-list'}" );

  return TRUE;

?>
