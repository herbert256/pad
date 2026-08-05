<?php

  // $padInfo selector 'go': deliberately a no-op.
  //
  // 'go' is the marker value info/start/tag.php assigns to $padInfo when a {trace} tag turns
  // info collection on mid-request without any named type. This file exists so that a later
  // re-read of the config list (inits/configSet.php walks $padInfo and includes
  // config/info/<word>.php) finds a file for it instead of failing.

  $padInfo = 'go';

?>