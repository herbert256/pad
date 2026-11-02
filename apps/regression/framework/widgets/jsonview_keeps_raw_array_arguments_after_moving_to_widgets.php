<?php

  $sample = [ 'name' => '<Ada>', 'items' => [ TRUE, NULL, 3 ] ];
  $html = padUnprotect ( padUnescape ( padCode ( '{widget:jsonview $sample, title="Payload"/}' ) ) );
  $answer = str_contains ( $html, '&lt;Ada&gt;' ) && str_contains ( $html, 'Payload' ) && str_contains ( $html, 'null' )
         ? 'arrays remain values and their strings are escaped' : 'FAIL';

?>
