<?php

  if ( isset ( $pad_parms_tag ['toContent'] ) ) {
    
    if ( $pad_tag == 'content' )
      pad_error ('@toContent not allowed with the {content/} tag');
 
  }

  if ( isset ( $pad_parms_tag ['toData'] ) ) {

    if ( $pad_tag == 'data' )
      pad_error ('@toDataS not allowed with the {data} tag');
 
  }

  if ( isset ( $pad_parms_tag ['walk'] ) and $pad_parms_tag ['walk'] !== TRUE )
    pad_error ('@walk may not have a value');

?>