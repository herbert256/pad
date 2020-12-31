<?php

  if ( isset ( $GLOBALS ['pad_content_store'] [ pad_tag_parm ('content') ] ) )
    $pad_content .= $GLOBALS ['pad_content_store'] [ pad_tag_parm ('content') ];
  else
    $pad_content .= pad_get ( pad_tag_parm ('content') );
  
?>