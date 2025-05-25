<?php

  $concepts ['sequences']  = 'Something that defines a Sequence list';
  $concepts ['stores']     = 'A stored sequence list';
  $concepts ['actions']    = 'An action executed on a Sequence list';
  $concepts ['plays']      = 'Execute a Sequence on a Sequence';
  $concepts ['continue']   = 'Continue on a stored Sequence';

  if ( ! isset ( $concept ) )
  	$concept = 'sequences';

  $conceptTitle = ucfirst ($concept ) . ' - ' . $concepts [$concept];

  $title .= ' - ' . ucfirst ($concept );

?>