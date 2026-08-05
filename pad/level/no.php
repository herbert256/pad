<?php

  // Fallback for a tag whose name resolved to no known type. With the optional option the
  // tag is dropped silently; otherwise it is put back into the output verbatim, escaped as
  // &open;..&close; so the loop will not pick it up again - just the tag when it is single,
  // the whole open..close span when a closing tag was found.

  if ( in_array ( 'optional', $padPrmParse ) )
    if ( padValidTag ($padWords [0]) )
      return include PAD . 'options/optional.php';

  if ( $padPairSet ) return padLevelNoPair   ();
  else               return padLevelNoSingle ();

?>