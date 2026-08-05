<?php

  // Resolves the bool="name" option to the flag of that name in $padBoolStore, falling back to
  // padMakeFlag() on the option value itself when the store holds no such flag.
  //
  // Nothing in the engine includes this file - no phase list names 'bool' - so a flag lookup
  // written in a template goes through types/bool.php ({bool:name}) instead.

  if ( ! isset ( $padBoolStore [ padTagParm('bool') ] ) )
    return padMakeFlag ( padTagParm('bool') );
  else
    return $padBoolStore [ padTagParm('bool') ];

?>