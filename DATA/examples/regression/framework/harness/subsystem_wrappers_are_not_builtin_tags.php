<?php

  include_once APPS . 'tags/_lib/catalog.php';
  include_once APPS . 'tags/_lib/kinds.php';
  include_once COMMON . '_lib/subsystemCatalog.php';
  $removed = explode ( ' ', 'chart game toy diagram widget barcode embed icon fake pattern sound printable notation section effect jsonld sparkline qr chess copy calendar' );
  $removedCount = 0;
  foreach ( $removed as $name )
    if ( ! is_file ( PAD . "tags/$name.php" ) && ! padTypeTagCheck ( 'pad', $name ) && ! isset ( tagsCatalog () ["pad:$name"] ) )
      $removedCount++;
  $answer = "$removedCount duplicate tags removed";

?>
