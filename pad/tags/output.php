<?php

   // The {output} tag: switches the response mode of the running request to web, console,
   // file, download, json or csv by loading config/output/<type>.php over the current
   // settings. json and csv answer the variables the page names in $padExpose instead of
   // what the template renders (lib/expose.php).
   //
   // $padOutputType is what exits/output.php dispatches on when the page is finished, so this
   // decides how the whole response is delivered, not just what happens at this level.
   //
   // The type is checked against the six that exist before it becomes part of an include
   // path; any other value is an error and the mode stays as it was.

   if ( ! in_array ( $padParm, [ 'web', 'console', 'file', 'download', 'json', 'csv' ], TRUE ) ) {
     padError ( "there is no output type named '$padParm'" );
     return FALSE;
   }

   $padOutputType = $padParm;

   include PAD . "config/output/$padOutputType.php";

   return TRUE;

?>