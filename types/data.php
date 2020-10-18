<?php

  if ( pad_tag_parm ('print') ) {

    $pad_content .= '{$' . $pad_name . '}';

    if ( pad_tag_parm ('glue') )
      $pad_content .= '{last}{else}' . pad_tag_parm ('glue') . '{/last}';

  }

  return $pad_data_store [$pad_tag];
 
?>