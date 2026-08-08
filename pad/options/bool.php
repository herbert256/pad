<?php

  // Resolves the bool="name" option to the flag of that name in $padBoolStore; a name the
  // store does not hold is FALSE, the way an unset flag reads everywhere else. The old
  // fallback ran padMakeFlag() over the option value itself, which made every unset flag
  // TRUE - any name is a non-empty string - the moment tags/if.php started including this.

  global $padBoolStore;

  if ( isset ( $padBoolStore [ padTagParm('bool') ] ) )
    return $padBoolStore [ padTagParm('bool') ];

  return FALSE;

?>