<?php

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $flags = 0;
  foreach ( $pad_sanitize as $sanitize )
    $flags = $flags | (int) "FILTER_FLAG_$sanitize";

  $pad_output = filter_var ( $pad_output, FILTER_UNSAFE_RAW, $flags );

?>