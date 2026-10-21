<?php

  // The page format/a_text_that_is_no_utf8_is_kept_in_a_json_answer asks for JSON: a name
  // as a latin1 database or file gives it, with a byte that is no UTF-8.

  $rows      = [ [ 'name' => "Caf\xE9", 'city' => 'Paris' ] ];
  $title     = "Caf\xE9 au lait";
  $padExpose = [ 'rows', 'title' ];

?>
