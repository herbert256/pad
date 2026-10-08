<?php

  // {can 'edit-post', $post}<a href="?edit&id={$id}">Edit</a>{else}read only{/can}: the
  // content when the user logged in may (padCan, lib/gate.php), the part after its own
  // {else} - or the @else@ half - when not. The first item is the ability, the others the
  // values its gate is given: an expression each, the name of an array of the page, or
  // the name of an enclosing loop - {posts}{can 'edit-post', $posts}...{/posts} - for the
  // row of its occurrence (padGateValue). {cannot} turns it round.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padCanItems = array_map ( fn ( $one ) => padGateValue ( $one ['padPrmOrg'] ), $padParms [$pad] );

  if ( ! count ( $padCanItems ) )
    padError ( "the {" . $padTag [$pad] . "} has no ability to ask for" );

  $padCanHeld = padCan ( ...$padCanItems );

  if ( $padTag [$pad] == 'cannot' )
    $padCanHeld = ! $padCanHeld;

  if ( padElseCut ( $padContent, $padCanHeld ) )
    return TRUE;

  return $padCanHeld;

?>
