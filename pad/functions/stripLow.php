<?php

  // Pipe function stripLow: meant to strip the ASCII control characters below 32 out of the
  // value. Note the flag is passed in filter_var's filter argument instead of its flags
  // argument, so as written the call reaches an unknown filter and returns FALSE.

  return filter_var ($value, FILTER_FLAG_STRIP_LOW);

?>