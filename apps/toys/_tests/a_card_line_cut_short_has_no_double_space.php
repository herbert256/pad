<?php

  // A card line cut at twenty words lost the dash it ended on and kept the space before it:
  // the spirograph's read "... and draws the curve  ...". The toys whose line holds two
  // spaces in a row, or none.

  $doubled = [];

  foreach ( toysCatalog () as $one )
    if ( str_contains ( $one ['line'], '  ' ) )
      $doubled [] = $one ['toy'];

  $doubled = $doubled ? implode ( ', ', $doubled ) : 'none';

?>
