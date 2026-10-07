<?php

  // A section whose rendering holds a {debug} box - shown to this machine's own requests
  // only - is rendered every time and never kept: built for a local request, the copy went
  // to every visitor after it, the value with it. The second section of the name finds no
  // stored copy and renders its own content.

  padFragmentForget ( 'fwDebugSection' );

  $debugValue = 'for this machine only';

?>
