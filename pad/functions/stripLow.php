<?php

  // Pipe function stripLow: strips the ASCII control characters below 32 out of the value.
  //
  // The flag belongs in filter_var's third argument; passing it as the second made it the
  // filter id, which is not a filter, so every call raised "Unknown filter with ID 4".

  return filter_var ( $value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW );

?>