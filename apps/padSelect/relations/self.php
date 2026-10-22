<?php

  // Two names for genres, related to it by parent_code: the parent of a genre, and the
  // genres whose parent it is.

  $padSelect ['parents']   = [ 'base' => 'genres' ];
  $padSelect ['subgenres'] = [ 'base' => 'genres' ];

  $padRelations ['genres']    ['parents'] = [ 'parent_code' => 'genre_code' ];
  $padRelations ['subgenres'] ['genres']  = [ 'parent_code' => 'genre_code' ];

?>
