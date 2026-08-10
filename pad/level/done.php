<?php

  if ( $padCheckSyntax )
    return padError ( "No open { found for closing } at position " . $padEnd [$pad] + 1);

  $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

?>