<?php

  // A body that is different on every build: nanoseconds. Two fetches answering with the
  // same body therefore prove the second came from the cache, not from a build.

  $stamp = hrtime ( TRUE );

?>