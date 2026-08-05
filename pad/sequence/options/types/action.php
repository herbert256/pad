<?php

  // Marker file, never included: its existence is what makes 'action' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // action='name|parm' names an actions/types/ handler explicitly, instead of writing the
  // action as the option itself. actions/inits.php takes the first '|' segment as the
  // action name and the rest as its parameter, and adds the pair to $pqActions.

  return TRUE;

?>