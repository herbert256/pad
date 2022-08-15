<?php

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $padSanitize_flags = 0;
  foreach ( $padSanitize as $padK)
    $padSanitize_flags = $padSanitize_flags | (int) "FILTER_FLAG_$padK";

  $padOutput = filter_var ( $padOutput, FILTER_UNSAFE_RAW, $padSanitize_flags );

?>