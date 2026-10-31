<?php

  $padSelect ['releases'] = [ 'base'   => 'albums',
                              'fields' => "title, released, 'album' as kind",
                              'union'  => 'singleReleases' ];

  $padSelect ['singleReleases'] = [ 'db' => 'singles', 'fields' => "title, released, 'single' as kind" ];

?>
