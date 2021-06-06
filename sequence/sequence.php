<?php

  $pad_walk_build = $pad_parms_tag ['build'] ?? 'before';

  if ( ! isset($pad_parms_tag ['walk']) )
    return include PAD_HOME . "sequence/build.php";

  return include PAD_HOME . "sequence/$pad_walk_build.php";

?>