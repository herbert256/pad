<?php

  // Pipe function sanitize: FILTER_SANITIZE_FULL_SPECIAL_CHARS - encodes <, >, & and both
  // quote styles as HTML entities. Same intent as html, done through the filter extension.

  return filter_var ( $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

?>