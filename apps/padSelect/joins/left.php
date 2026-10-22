<?php

  $padSelect ['labelName'] = [ 'db' => 'labels', 'key' => 'label_id', 'fields' => [ 'name' => 'label' ] ];

  $padSelect ['artistsLabels'] = [ 'base'   => 'artists',
                                   'fields' => 'artists.name, artists.country',
                                   'join'   => [ 'left' => 'labelName', 'key' => 'artists.signed_to' ],
                                   'order'  => 'artists.name' ];

?>
