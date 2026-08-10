<?php

  // Fires from level/start.php once the level's parameters are parsed and before the tag
  // handler runs - deliberately before: a tag that jumps ({break}, {continue}), ends the
  // request ({exit}, {redirect}) or errors in its handler never reaches the after-the-fact
  // events, and used to stay off the xref record because of it.
  //
  // Records the tag under its type (with any @property suffix stripped off) plus, for
  // plain 'tag' types, an entry in the properties index.

  global $padInfoXref;

  if ( $padInfoXref ) {

    if ( str_contains ( $padTag [$pad], '@' ) )
      $padInfoTmp = strstr ( $padTag [$pad], '@', true );
    else
      $padInfoTmp = $padTag [$pad];

    padInfoXref ( 'tag', $padType [$pad], $padInfoTmp );

    if ( $padType [$pad] == 'tag' )
      padInfoXref ( 'properties', $padInfoTmp );

  }

?>