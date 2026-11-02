<?php

  // Pipe function open: builds the literal text of a PAD opening tag from the value - open
  // on 'array:items' yields '{array:items}'. tag is the same function; close builds '{/array:items}'.

  return "{" . $value . "}";

?>
