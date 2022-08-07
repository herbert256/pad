<?php

  // Default filter options on the complete output, executed before Tidy
  // Must be a flag from FILTER_UNSAFE_RAW from below page.
  // https://www.php.net/manual/en/filter.filters.sanitize.php

  $pSanitize_flags = 0;
  foreach ( $pSanitize as $pK)
    $pSanitize_flags = $pSanitize_flags | (int) "FILTER_FLAG_$pK";

  $pOutput = filter_var ( $pOutput, FILTER_UNSAFE_RAW, $pSanitize_flags );

?>