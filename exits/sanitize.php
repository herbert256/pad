<?php

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $pad_sanitize_flags = 0;
  foreach ( $pad_sanitize as $pad_k)
    $pad_sanitize_flags = $pad_sanitize_flags , (int) "FILTER_FLAG_$pad_k";

  $pad_output = filter_var ( $pad_output, FILTER_UNSAFE_RAW, $pad_sanitize_flags );

?>