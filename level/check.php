<?php

  if ( isset ( $pad_parms_pad ['toContentStore'] ) and $pad_parms_pad ['toContentStore'] === TRUE )
    pad_error ('@toContentStore must have a value');

  if ( isset ( $pad_parms_pad ['toDataStore'] ) and $pad_parms_pad ['toDataStore'] === TRUE )
    pad_error ('@toDataStore must have a value');

  if ( isset ( $pad_parms_pad ['walk'] ) and $pad_parms_pad ['walk'] !== TRUE )
    pad_error ('@walk may not have a value');

  if ( isset ( $pad_parms_pad ['walk'] ) and  isset ( $pad_parms_pad ['toDataStore'] ) )
    pad_error ('@walk and @toDataStore can not be used together');

?>