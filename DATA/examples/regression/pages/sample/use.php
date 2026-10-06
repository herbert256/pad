<?php

  // ?sample/orders&padSample: the page with its sample - no _inits.php, no page PHP, and
  // the named {field} answered from the sample. The engine name in the sample is ignored.

  $useResult = padCurl ( [ 'url' => $padGoExt . 'sample/orders&padSample&padInclude' ] ) ['data'];

?>
