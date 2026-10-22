<?php

  // The model: the tables of the music catalogue (_install/padSelect.sql) and how they reach
  // one another. Every example of this application stands on these lines; one that needs a
  // view, a join or a union declares it in its own .php.
  //
  // A table is declared with its key - the field, or the fields, that name one row - and the
  // order its rows come in when the tag gives none.

  $padSelect ['labels']    = [ 'key' => 'label_id',              'order' => 'name'          ];
  $padSelect ['artists']   = [ 'key' => 'artist_id',             'order' => 'name'          ];
  $padSelect ['genres']    = [ 'key' => 'genre_code',            'order' => 'genre'         ];
  $padSelect ['albums']    = [ 'key' => 'album_id',              'order' => 'released'      ];
  $padSelect ['tracks']    = [ 'key' => 'album_id,track_no'                                 ];
  $padSelect ['singles']   = [ 'key' => 'single_id',             'order' => 'released'      ];
  $padSelect ['reviews']   = [ 'key' => 'review_id',             'order' => 'written'       ];
  $padSelect ['playlists'] = [ 'key' => 'playlist_id',           'order' => 'name'          ];

  // A tag needs no table of its own name: entries reads playlist_tracks.

  $padSelect ['entries']   = [ 'db'  => 'playlist_tracks', 'key' => 'playlist_id,position', 'order' => 'position' ];

  // A relation is declared from the table that holds the reference: $padRelations [a] [b]
  // is how a row of a finds its row of b - and, read the other way, how b finds its rows of
  // a. It is written in one of three forms:
  //
  //   'artist_id'                         a field both tables have
  //   [ 'key' => 'signed_to' ]            the field of a holding the declared key of b
  //   [ 'album_id' => 'album_id', ... ]   field of a => field of b, one pair per field

  $padRelations ['albums']  ['artists']   = 'artist_id';
  $padRelations ['albums']  ['genres']    = 'genre_code';
  $padRelations ['singles'] ['artists']   = 'artist_id';
  $padRelations ['tracks']  ['albums']    = 'album_id';
  $padRelations ['reviews'] ['albums']    = 'album_id';
  $padRelations ['artists'] ['labels']    = [ 'key' => 'signed_to' ];
  $padRelations ['entries'] ['playlists'] = 'playlist_id';
  $padRelations ['entries'] ['tracks']    = [ 'album_id' => 'album_id', 'track_no' => 'track_no' ];

?>
