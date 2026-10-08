<?php

  padSse ( function ( $send, $last ) {

    $send ( 'resume', [ 'after' => $last ] );

  } );

?>
