<?php

  // A CSV answer of a record is one row, a list in it a cell of JSON - it was a line per
  // value under the header order,0,1 - and of a list of objects a row per object, its
  // properties the columns, as the JSON answer shows them - it was one column of JSON.

  foreach ( [ 'order', 'rows' ] as $recordsWhich ) {
    $recordsCsv = padCurl ( $padGoExt . "format/records&padFormat=csv&which=$recordsWhich" );
    echo "$recordsWhich: ", $recordsCsv ['result'], ' ', str_replace ( "\n", ' | ', trim ( $recordsCsv ['data'] ) ), "\n";
  }

?>
