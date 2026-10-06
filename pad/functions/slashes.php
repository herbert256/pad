<?php

  // Pipe function slashes: addslashes, backslash-escaping quotes, backslashes and NUL so the
  // value can sit inside a quoted SQL or JavaScript string. stripslashes is the inverse.
  // The stand-ins of the quotes and the backslash are escaped as those characters
  // (padUnprotectQuotes): left alone they became a live quote or backslash at output.

  return addslashes ( padUnprotectQuotes ( $value ) );

?>
