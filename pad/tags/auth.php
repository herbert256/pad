<?php

  // {auth}Hello {$name}{else}<a href="?login">Log in</a>{/auth}: the content when someone
  // is logged in (padLogin, lib/auth.php), with the user's row as its data - every field of
  // it a {$field} inside - and the part after its own {else}, or the @else@ half, for a
  // guest. The row's password and token fields are never there: padLogin keeps them out.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padAuthUser = padUser ();

  if ( padElseCut ( $padContent, $padAuthUser !== NULL ) )
    return ( $padAuthUser !== NULL ) ? [ $padAuthUser ] : TRUE;

  return ( $padAuthUser !== NULL ) ? [ $padAuthUser ] : FALSE;

?>
