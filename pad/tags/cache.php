<?php

  // {cache 'name', ttl=300} ... {/cache}: a named section of the page kept rendered for ttl
  // seconds - lib/fragment.php does the work, from level/start.php and level/end.php. On a
  // miss this handler runs and the content renders as it stands; on a hit it never runs.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {cache} never closes" );

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( "the {cache} needs a name - {cache 'top-products'}" );

  return TRUE;

?>
