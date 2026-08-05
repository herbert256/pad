<?php

   // The {output} tag: switches the response mode of the running request to web, console,
   // file or download by loading config/output/<type>.php over the current settings.
   //
   // $padOutputType is what exits/output.php dispatches on when the page is finished, so this
   // decides how the whole response is delivered, not just what happens at this level.

   $padOutputType = $padParm;

   include PAD . "config/output/$padOutputType.php";

   return TRUE;

?>