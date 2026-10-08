<?php

  // TRUE, FALSE, a share - decided by the id, fixed here - and a function given the user.

  function featureCaseBeta ( $user ) { return ( $user ['role'] ?? '' ) == 'beta'; }

  $padFeatures = [ 'on' => TRUE, 'off' => FALSE, 'all' => 1, 'none' => 0.0, 'half' => 0.5, 'beta' => 'featureCaseBeta' ];

  padLogin ( [ 'id' => 3, 'role' => 'beta' ] );
  padFeatureId ( 'visitor:case' );

?>
