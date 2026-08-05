<?php

  // Pipe function optional: null-coalesces the value to the empty string, so a null renders
  // as nothing further down the pipe. The same word written as a tag option is a different
  // thing (options/optional.php) - that one suppresses the missing-value error.

  return $value ?? '';

?>