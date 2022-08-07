<?php

  if ( pTag_parm ('open')  ) include PAD . 'options/open.php';

  $pContent .= '{$' . $pName[$p] . '}';

  if ( pTag_parm ('glue')  ) include PAD . 'options/glue.php';
  if ( pTag_parm ('close') ) include PAD . 'options/close.php';

?>