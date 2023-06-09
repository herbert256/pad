<?php

  $padIdx = $i;

  $tag  = $names [0] ?? '';
  $parm = $names [1] ?? '';;

  return include pad . "tag/$tag.php";

?>