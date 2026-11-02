<?php

  // {pad:feature 'search2'}<new search>{pad:else}<old search>{/pad:feature}: the content when the flag
  // is on for this visitor (padFeature, lib/feature.php), the part after its own {pad:else} -
  // or the @else@ half - when it is off.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  $padFeatureHeld = padFeature ( (string) $padParm );

  if ( padElseCut ( $padContent, $padFeatureHeld ) )
    return TRUE;

  return $padFeatureHeld;

?>
