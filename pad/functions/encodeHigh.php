<?php

  // Pipe function encodeHigh: encodes every character above ASCII 127 as a numeric HTML
  // entity. Per character, not per byte: the filter_var flag this used to lean on encoded
  // each UTF-8 byte on its own, so 'café' answered caf&#195;&#169; and a browser showed
  // mojibake where caf&#233; was meant.

  return mb_encode_numericentity ( $value, [ 0x80, 0x10FFFF, 0, 0x10FFFF ], 'UTF-8' );

?>