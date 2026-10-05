<?php

  // {fragment 'order-list'} ... {/fragment}: a named part of the page. It renders in place;
  // a request that names it - &padFragment=order-list - gets it alone, which level/end.php
  // sees to when the fragment's level closes (lib/respond.php).

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {fragment} never closes" );

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( "the {fragment} needs a name - {fragment 'order-list'}" );

  return TRUE;

?>
