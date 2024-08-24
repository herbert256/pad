<?php
  
  $padSeqActions = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) ) {

      if ( $padPrmValue === TRUE or ! ctype_digit($padPrmValue) )
        if ( $padSeqCnt )
          $padSeqActionCnt = $padSeqCnt;
        else
          $padSeqActionCnt = 1;
      else
        $padSeqActionCnt = $padPrmValue;    

      $padSeqActions [] = [
        'padSeqActionName'  => $padPrmName, 
        'padSeqActionValue' => $padPrmValue,
        'padSeqActionCnt'   => $padSeqActionCnt,
        'padSeqActionList'  => padExplode ( $padPrmValue, '|' ),
      ];

    }

  }
  
?>