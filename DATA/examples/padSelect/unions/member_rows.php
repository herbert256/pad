<?php

  // A member with rows= of its own - and the order that picks them - adds those rows only.

  $padSelect ['latestSingles'] = [ 'db' => 'singles', 'fields' => "title, released, 'single' as kind",
                                   'order' => 'released desc', 'rows' => 2 ];

  $padSelect ['latest'] = [ 'base'   => 'albums',
                            'fields' => "title, released, 'album' as kind",
                            'where'  => 'released >= 2016',
                            'union'  => 'latestSingles' ];

?>
