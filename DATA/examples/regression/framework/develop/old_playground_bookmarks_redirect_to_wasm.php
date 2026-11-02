<?php

  $old = padCurl ( [ 'url' => $padHost . 'playground/', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );
  $post = padCurl ( [ 'url' => $padHost . 'playground/?render', 'post' => [ 'source' => 'must not run' ],
                      'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );
  $answer = $old ['result'] . ': ' . ( $old ['headers'] ['Location'] ?? '' ) . ' / POST: ' . $post ['result'];

?>
