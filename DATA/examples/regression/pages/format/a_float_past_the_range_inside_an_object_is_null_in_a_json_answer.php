<?php

  // JSON has no INF or NAN, inside an object no more than inside an array
  // (a_float_past_the_range_is_null_in_a_json_answer): a JSON answer writes them null, where
  // an object - a json_decode row, a cast array, a JsonSerializable - kept them for the
  // partial output, which wrote 0.

  $infJson = padCurl ( $padGoExt . 'format/infobjects&padFormat=json' );

  echo $infJson ['result'], ' ', json_encode ( json_decode ( $infJson ['data'] ) );

?>
