<?php

  // {guest}<a href="?login">Log in</a>{else}<a href="?logout">Log out</a>{/guest}: the
  // content when nobody is logged in (lib/auth.php), the part after its own {else} - or
  // the @else@ half - when someone is. The user's fields are {auth}'s to give.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padGuestHeld = ! padAuthCheck ();

  if ( padElseCut ( $padContent, $padGuestHeld ) )
    return TRUE;

  return $padGuestHeld;

?>
