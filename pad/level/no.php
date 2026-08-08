<?php

  // Fallback for a tag whose name resolved to no known type. With the optional or noError
  // option the tag is dropped silently; otherwise it is put back into the output verbatim,
  // escaped as &open;..&close; so the loop will not pick it up again - just the tag when it
  // is single, the whole open..close span when a closing tag was found. noError used to be
  // parsed and ignored, so the tag it was written on leaked its own source into the page.

  if ( in_array ( 'optional', $padPrmParse ) or in_array ( 'noError', $padPrmParse ) )
    if ( padValidTag ($padWords [0]) )
      return include PAD . 'options/optional.php';

  if ( $padPairSet ) return padLevelNoPair   ();
  else               return padLevelNoSingle ();

?>