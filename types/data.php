<?php

  if ( ! trim($pad_content) and ! pad_tag_parm ('content') ) {

    $pad_content = '{$' . $pad_name . '}';

    if ( pad_tag_parm ('glue') )
     $pad_content .= '{last}{else}' . pad_tag_parm ('glue') . '{/last}';

  }

  return $pad_data_store [$pad_tag];
 
?>