<?php

   // The {ignore} tag: escapes everything it wraps so the engine stops seeing PAD markup in
   // it. padEscape swaps { } | = , and @ for their &open;-style entities, which exits.php
   // turns back into the literal characters on the way out - the way inline JavaScript, CSS
   // and JSON keep their braces.

   $padContent = padEscape ( $padContent );

   return true;

?>