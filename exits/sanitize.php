<?php

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $padSanitizeFlags = 0;
  foreach ( $padSanitize as $padK)
    $padSanitizeFlags = $padSanitizeFlags | (int) "FILTER_FLAG_$padK";

  $padOutput = filter_var ( $padOutput, FILTER_UNSAFE_RAW, $padSanitizeFlags );

?>