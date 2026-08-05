<?php

  // Pipe function encodeHigh: encodes every byte above ASCII 127 as a numeric HTML entity.
  // It works byte-wise, so one UTF-8 character comes out as one entity per byte.

  return filter_var ($value, FILTER_DEFAULT, ['flags' => FILTER_FLAG_ENCODE_HIGH]);

?>