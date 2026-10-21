<?php

  // The request brings max, date and upper, and the page reads each - in a string, in a
  // closure's use ( ), as $GLOBALS ['upper'] inside a function - so each is the page's own
  // and its bare tag the field, not PHP's max (), the date pipe or the upper pipe.

  padGetParms ( 'GET', [ 'max' => 'M', 'date' => 'D', 'upper' => 'U' ] );

  $zzMsg  = "asked $max";
  $zzHits = array_filter ( [ 1 ], function ( $one ) use ( $date ) { return $one; } );
  $zzPick = function () { $GLOBALS ['upper'] = $GLOBALS ['upper'] ?? 'none'; };

  $zzPick ();

?>
