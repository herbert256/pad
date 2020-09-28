<?php

  if ( isset ( $pad_parms_tag ['toContentStore'] ) and $pad_parms_tag ['toContentStore'] === TRUE )
    pad_error ('@toContentStore must have a value');

  if ( isset ( $pad_parms_tag ['toDataStore'] ) and $pad_parms_tag ['toDataStore'] === TRUE )
    pad_error ('@toDataStore must have a value');

  if ( isset ( $pad_parms_tag ['toFlagStore'] ) and $pad_parms_tag ['toFlagStore'] === TRUE )
    pad_error ('@toFlagStore must have a value');

  if ( isset ( $pad_parms_tag ['walk'] ) and $pad_parms_tag ['walk'] !== TRUE )
    pad_error ('@walk may not have a value');

  if ( isset ( $pad_parms_tag ['walk'] ) and  isset ( $pad_parms_tag ['toDataStore'] ) )
    pad_error ('@walk and @toDataStore can not be used together');

?>