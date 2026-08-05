<?php

  // Pipe function url: urlencode, making the value safe to drop into a query string - spaces
  // become '+' and reserved characters percent-escapes.

  return urlencode ($value);

?>