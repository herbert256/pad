<?php

  // A } with no { before it: no tag holds it, so the report points at the brace itself.

  if ( $padCheckSyntax )
    padErrorAt ( "No open { found for closing } at position " . $padEnd [$pad] + 1,
                 [ 'level' => $pad, 'out' => $padEnd [$pad], 'length' => 1 ] );

  $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

?>