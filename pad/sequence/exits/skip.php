<?php

  foreach ( $padSeqOptions as $padSeqOptionKey => $padSeqOption ) {

    extract ( $padSeqOption );
  
    if ( $padPrmName == $padSeqSeq and $padPrmValue ==$padSeqParm )  {
      unset ( $padSeqOptions [$padSeqOptionKey] );
      return;
    }
    
  }

?>