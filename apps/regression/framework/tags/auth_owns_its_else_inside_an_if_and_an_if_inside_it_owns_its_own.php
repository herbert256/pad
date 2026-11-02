<?php

  // An {pad:else} belongs to the innermost owner it stands in - {pad:if}, {pad:case}, {pad:auth}, {pad:guest},
  // {pad:can}, {pad:cannot}, {pad:feature} - whichever of them encloses which.

  padLogin ( [ 'id' => 1, 'name' => 'Bo' ] );

  padGate ( 'x', fn ( $user ) => FALSE );

  $padFeatures = [ 'off' => FALSE ];

?>
