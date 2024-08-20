<?php
  
  foreach ( $padPrmParse as $padV ) {

    list ( $padSeqActionName , $padSeqActionValue ) = padSplit ( '=', trim($padV) );

    if ( file_exists ( "/pad/sequence/actions/types/$padSeqActionName.php" ) ) {

      $padSeqActionValue = padEval ( $padSeqActionValue ) ;

      if ( $padSeqActionValue === TRUE or ! ctype_digit($padSeqActionValue) )
        if ( $padSeqCnt )
          $padSeqActionCnt = $padSeqCnt;
        else
          $padSeqActionCnt = 1;
      else
        $padSeqActionCnt = $padSeqActionValue;    

      if ( $GLOBALS ['padInfo'] ) 
        include '/pad/events/action.php';
 
      $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

      padDone ( $padSeqActionName, TRUE );

    }

  }
  
?>