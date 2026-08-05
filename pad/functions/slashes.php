<?php

  // Pipe function slashes: addslashes, backslash-escaping quotes, backslashes and NUL so the
  // value can sit inside a quoted SQL or JavaScript string. stripslashes is the inverse.

  return addslashes ($value);

?>