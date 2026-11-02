<?php

  // Pipe function tag: alias of open - wraps the value in braces, so tag on 'array:items' yields
  // the literal text '{array:items}'.

  return "{" . $value . "}";

?>
