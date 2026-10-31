<?php

  // A link as a request brings it: U+E022 is the stand-in of the double quote
  // (lib/encoding.php), which exits.php turns back into the quote as the page is written out
  // - after every escaper has run. A row of data holds it as the request handed it, and a
  // raw field written in the lines of a pair carries its real quote as that stand-in.

  $next  = "?x\u{E022} onmouseover=\u{E022}alert(1)";
  $trail = [ [ 'label' => 'Home', 'link' => '?index' ],
             [ 'label' => 'Back', 'link' => $next ],
             [ 'label' => 'Here' ] ];
  $back  = '?y" onmouseover="alert(2)';

?>
