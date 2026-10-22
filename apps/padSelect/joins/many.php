<?php

  $padSelect ['albumTitle'] = [ 'db' => 'albums',  'key' => 'album_id',  'fields' => [ 'title' => 'album' ] ];
  $padSelect ['artistName'] = [ 'db' => 'artists', 'key' => 'artist_id', 'fields' => [ 'name'  => 'artist' ] ];

  $padSelect ['longest'] = [ 'base'   => 'tracks',
                             'fields' => 'tracks.title, tracks.seconds',
                             'join'   => [ [ 'inner' => 'albumTitle', 'key' => 'tracks.album_id' ],
                                           [ 'inner' => 'artistName', 'key' => 'albums.artist_id' ] ],
                             'order'  => 'tracks.seconds desc',
                             'rows'   => 6 ];

?>
