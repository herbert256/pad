<?php

  if ( pad_tag_parm ('open')  ) include PAD . 'options/open.php';

  $pad_content .= '{$' . $pad_name . '}';

  if ( pad_tag_parm ('glue')  ) include PAD . 'options/glue.php';
  if ( pad_tag_parm ('close') ) include PAD . 'options/close.php';

?>