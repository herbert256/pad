<?php
  // Validation only: canonical Semantic Versioning 2.0.0.
  return [
    'check' => fn ( $value ) => preg_match ( '/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-(?:0|[1-9][0-9]*|[0-9]*[A-Za-z-][0-9A-Za-z-]*)(?:\.(?:0|[1-9][0-9]*|[0-9]*[A-Za-z-][0-9A-Za-z-]*))*)?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?$/D', $value ) === 1,
    'message' => ':label must be a valid SEMVER'
  ];
?>
