<?php

  // Physical conversion helpers; definitions load only when first used.
  function padUnits ( $family = NULL ) {
    include_once PAD . 'subs/units/_lib/units.php';
    return padUnitsDefinitions ( $family );
  }

  function padConvert ( $value, $from, $to ) {
    include_once PAD . 'subs/units/_lib/units.php';
    return padUnitsConvert ( $value, $from, $to );
  }

?>
