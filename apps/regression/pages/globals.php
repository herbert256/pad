<?php

  // object: reads $GLOBALS, and a sandboxed pass has the application globals taken out of it, so
  // the sandbox case for it has to render in the runner's own scope. In a real request a page
  // variable is a global like any other and the reference resolves without help.

  $colours = [ 'red', 'green' ];

?>