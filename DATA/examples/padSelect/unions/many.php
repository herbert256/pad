<?php

  $padSelect ['names']       = [ 'db' => 'labels',    'fields' => "name, 'label' as what",    'union' => [ 'artistNames', 'listNames' ] ];
  $padSelect ['artistNames'] = [ 'db' => 'artists',   'fields' => "name, 'artist' as what"   ];
  $padSelect ['listNames']   = [ 'db' => 'playlists', 'fields' => "name, 'playlist' as what" ];

?>
