<?php

  // {pad:can 'edit-post', $post}<a href="?edit&id={$id}">Edit</a>{pad:else}read only{/pad:can}: the
  // content when the user logged in may (padCan, lib/gate.php), the part after its own
  // {pad:else} - or the @else@ half - when not. The first item is the ability, the others the
  // values its gate is given: an expression each, the name of an array of the page, or
  // the name of an enclosing loop - {array:posts}{pad:can 'edit-post', $posts}...{/array:posts} - for the
  // row of its occurrence (padGateValue). {pad:cannot} turns it round.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padCanItems = array_map ( fn ( $one ) => padGateValue ( $one ['padPrmOrg'] ), $padParms [$pad] );

  if ( ! count ( $padCanItems ) )
    padError ( "the {" . padTagDisplay () . "} has no ability to ask for" );

  $padCanHeld = padCan ( ...$padCanItems );

  if ( $padTag [$pad] == 'cannot' )
    $padCanHeld = ! $padCanHeld;

  if ( padElseCut ( $padContent, $padCanHeld ) )
    return TRUE;

  return $padCanHeld;

?>
