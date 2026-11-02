<?php

  // {pad:stack 'scripts'/}: everything pushed to the stack of that name, in the order it was
  // pushed - by the parts of the page above this tag and by those below it alike, since
  // the tag prints a marker that is filled in once the whole page has rendered
  // (padStackFill in lib/stack.php, from exits/exits.php).

  if ( trim ( (string) $padParm ) === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {pad:stack} needs a name - {pad:stack 'scripts'/}" );
    return '';
  }

  return padStackMarker ( $padParm );

?>
