<?php

  // {pad:guest}<a href="?login">Log in</a>{pad:else}<a href="?logout">Log out</a>{/pad:guest}: the
  // content when nobody is logged in (lib/auth.php), the part after its own {pad:else} - or
  // the @else@ half - when someone is. The user's fields are {pad:auth}'s to give.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padGuestHeld = ! padAuthCheck ();

  if ( padElseCut ( $padContent, $padGuestHeld ) )
    return TRUE;

  return $padGuestHeld;

?>
