<?php

  if ( $padType [$pad] == 'pad' )
    if ( str_contains ( $padXrefPageSouce, '{' . $padTag [$pad] ) )
      return padXref ( 'tags', 'pad', $padTag [$pad] );

  padXref ( 'skipped', $padTag [$pad] );

?>