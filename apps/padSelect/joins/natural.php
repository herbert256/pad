<?php

  $padSelect ['albumGenres'] = [ 'base'   => 'albums',
                                 'fields' => 'title, genre',
                                 'join'   => [ 'natural' => 'genres' ],
                                 'order'  => 'genre, title' ];

?>
