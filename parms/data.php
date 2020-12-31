<?php

  if ( isset ( $GLOBALS ['pad_data_store'] [ pad_tag_parm ('data') ] ) )
    pad_add_array_to_data ( $GLOBALS ['pad_data_store'] [ pad_tag_parm ('data') ] ), pad_tag_parm('type') );
  else
    pad_add_array_to_data ( pad_get ( pad_tag_parm ('data'), 'data' ), pad_tag_parm('type') );
  
?> 