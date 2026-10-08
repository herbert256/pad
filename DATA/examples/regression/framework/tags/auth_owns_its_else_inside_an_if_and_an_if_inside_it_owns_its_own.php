<?php

  // An {else} belongs to the innermost owner it stands in - {if}, {case}, {auth}, {guest},
  // {can}, {cannot}, {feature} - whichever of them encloses which.

  padLogin ( [ 'id' => 1, 'name' => 'Bo' ] );

  padGate ( 'x', fn ( $user ) => FALSE );

  $padFeatures = [ 'off' => FALSE ];

?>
