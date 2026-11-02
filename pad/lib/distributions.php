<?php
  function padSample ( $kind, $count = 1, $options = [] ) {
    include_once PAD . 'subs/distributions/_lib/distributions.php';
    return padDistributionSample ( $kind, $count, $options );
  }
?>
