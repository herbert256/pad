<?php

  // ?sample/rows&padSample: the rows the page returns come from the sample's @return - no
  // page PHP runs.

  $useRows = padCurl ( [ 'url' => $padGoExt . 'sample/rows&padSample&padInclude' ] ) ['data'];

?>
