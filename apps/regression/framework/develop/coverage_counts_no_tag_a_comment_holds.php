<?php

  // A tag inside a comment never runs, so it is no item of the template: the {-- --} form
  // was read as text and every tag in it listed - and marked as never run - and a { left
  // unclosed inside a {# #} comment carried the scan past its end.

  $coverSource = "{-- {if \$old eq 1}gone{/if} --}{# an { open brace #}{items}{\$x}{/items}{#x}";
  $coverTexts  = implode ( ' ', array_column ( padCoverageItems ( $coverSource ), 'text' ) );

?>
