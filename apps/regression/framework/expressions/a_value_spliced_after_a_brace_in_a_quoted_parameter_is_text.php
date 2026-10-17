<?php

  // A value spliced into a tag's parameters after a { that opens no tag - JSON written in a
  // quoted string, '{"q": ' - is a string of the expression: the splice took that { for the
  // tag's own, judged the value to stand in text, and left it bare, and the request value
  // 0 + php:abs(-3) ran as part of the {echo}, which printed {"q": 3}. JSON in the page's
  // text keeps its value as it is.

  $q = '0 + php:abs(-3)';
  $r = 'Bob Smith';

?>
