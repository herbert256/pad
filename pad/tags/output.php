<?php

   // The {output} tag: switches the response mode of the running request to web, console,
   // file or download by loading config/output/<type>.php over the current settings.
   //
   // $padOutputType is what exits/output.php dispatches on when the page is finished, so this
   // decides how the whole response is delivered, not just what happens at this level.
   //
   // The type is checked against the four that exist before it becomes part of an include
   // path; any other value is an error and the mode stays as it was.

   if ( ! in_array ( $padParm, [ 'web', 'console', 'file', 'download' ], TRUE ) ) {
     padError ( "there is no output type named '$padParm'" );
     return FALSE;
   }

   $padOutputType = $padParm;

   include PAD . "config/output/$padOutputType.php";

   return TRUE;

?>