<?php
  // Validation only: canonical uppercase Crockford ULID, overflow excluded.
  return [
    'check' => fn ( $value ) => preg_match ( '/^[0-7][0-9A-HJKMNP-TV-Z]{25}$/D', $value ) === 1,
    'message' => ':label must be a valid ULID'
  ];
?>
