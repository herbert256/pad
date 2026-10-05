<?php

  // The guard fixture: every page below this directory opens only for a request that
  // brings key=open.

  return ( $_GET ['key'] ?? '' ) === 'open';

?>
