<?php

  // The joined table is declared like any other, with the columns it adds to the row.

  $padSelect ['artistName'] = [ 'db' => 'artists', 'key' => 'artist_id', 'fields' => [ 'name' => 'artist' ] ];

  $padSelect ['albumsBy'] = [ 'base'   => 'albums',
                              'fields' => 'albums.title, albums.released',
                              'join'   => [ 'inner' => 'artistName', 'key' => 'albums.artist_id' ],
                              'order'  => 'artist, released' ];

?>
