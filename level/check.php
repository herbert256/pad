<?php

  if ( isset ( $pad_parms_tag ['toContentStore'] ) ) {
    
    if ( $pad_tag == 'content' )
      pad_error ('@toContentStore not allowed with the {content} tag');
 
  }

  if ( isset ( $pad_parms_tag ['toDataStore'] ) ) {

    if ( $pad_tag == 'data' )
      pad_error ('@toDataStore not allowed with the {data} tag');
 
  }

  if ( isset ( $pad_parms_tag ['walk'] ) and $pad_parms_tag ['walk'] !== TRUE )
    pad_error ('@walk may not have a value');

?>