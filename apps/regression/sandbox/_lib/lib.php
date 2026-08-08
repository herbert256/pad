<?php

  // $seqFixture is the list the 'scope' cases in sequence/library.php iterate, and $objFixture
  // the variable the object: case in expressions/references.php reads. Both are declared here
  // rather than on one page because the overview runs every group as well, and padCode() renders
  // its pass over the globals - a variable local to getCases() or set on one page only would
  // leave the other page reporting a missing field.
  //
  // $objFixture cannot be handed over as a fourth-element setup the way other data is: object:
  // reads $GLOBALS, and a sandboxed pass has the application globals taken out of it, which is
  // what sandboxing means. So that case renders in this file's scope instead.

  $seqFixture = [ 1, 2, 3, 4, 5 ];
  $objFixture = [ 'a', 'b' ];

?>