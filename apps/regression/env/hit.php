<?php

  // One hit on the rate limit the burst page counts.

  $counted = padRateLimit ( 'burst', 1000, 60 ) ? 'counted' : 'refused';

?>
