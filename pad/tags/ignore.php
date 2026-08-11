<?php

   // The {ignore} tag: escapes everything it wraps so the engine stops seeing PAD markup in
   // it. padEscape swaps { } | = , and @ for their &open;-style entities, which exits.php
   // turns back into the literal characters on the way out - the way inline JavaScript, CSS
   // and JSON keep their braces.

   // An {ignore} without its {/ignore} protects nothing, and what it meant to cover gets
   // parsed - the error then blames some inner tag, far from the cause. Strict mode names
   // the cause.

   if ( ! $padPair [$pad] and $padCheckSyntax )
     padError ( "the pair {ignore} never closes" );

   $padContent = padEscape ( $padContent );

   return true;

?>