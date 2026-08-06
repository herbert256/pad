<?php

  // Front page: names the five ideas the subsystem is built from, and describes the one the
  // concept= parameter asks for.
  //
  // concept arrives from the query string, so it is only used when it names one of them -
  // reading $concepts with an unknown key ended the request with a 500 instead of a page.

  $concepts ['sequences']  = 'Something that defines a Sequence list';
  $concepts ['stores']     = 'A stored sequence list';
  $concepts ['actions']    = 'An action executed on a Sequence list';
  $concepts ['plays']      = 'Execute a Sequence on a Sequence';
  $concepts ['resume']     = 'Resume on a stored Sequence';

  if ( ! isset ( $concept ) or ! isset ( $concepts [$concept] ) )
    $concept = 'sequences';

  $title = 'Sequences';

  $conceptTitle = ucfirst ($concept ) . ' - ' . $concepts [$concept];

?>