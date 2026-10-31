<?php

  // A view of the jazz genres, joined: only its rows join, so only the albums of a jazz
  // genre come back. Its where names the column within the view, not the albums.

  $padSelect ['jazz'] = [ 'db' => 'genres', 'key' => 'genre_code', 'where' => "parent_code = 'jazz' or genre_code = 'jazz'",
                          'fields' => [ 'genre' => 'style' ] ];

  $padSelect ['jazzAlbums'] = [ 'base'   => 'albums',
                                'fields' => 'albums.title, albums.released',
                                'join'   => [ 'inner' => 'jazz', 'key' => 'albums.genre_code' ],
                                'order'  => 'albums.released' ];

?>
