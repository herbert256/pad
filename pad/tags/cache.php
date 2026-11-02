<?php

  // {pad:cache 'name', ttl=300} ... {/pad:cache}: a named section of the page kept rendered for ttl
  // seconds - lib/fragment.php does the work, from level/start.php and level/end.php. On a
  // miss this handler runs and the content renders as it stands; on a hit it never runs.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {pad:cache} never closes" );

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( "the {pad:cache} needs a name - {pad:cache 'top-products'}" );

  return TRUE;

?>
