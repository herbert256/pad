<?php

  $padSeqOprSeq = $padSeqOne;

  foreach ( $padSeqOprList as $padSeqOpr ) {

    extract ( $padSeqOpr );

    $padSeqParm = $GLOBALS [ padSeqName ($padSeqOprName) ] = $padSeqOprValue;

    $padSeqLoop = $padSeqOprSeq;

    if ( $padSeqOprType == 'keep' or $padSeqOprType == 'remove' ) 
      if ( file_exists ( "/pad/sequence/types/$padSeqOprName/bool.php" ) )
        $padSeqOprBld = 'bool';

    if ( in_array ( $padSeqOprBld, [ 'fixed','order','jump'] ) ) {
      $padSeqOprBld = '';
    }

    if     ( $padSeqOprBld == 'function' ) $padSeqOprNow = ( 'padSeq'     . ucfirst($padSeqOprName) ) ( $padSeqLoop );
    elseif ( $padSeqOprBld == 'bool'     ) $padSeqOprNow = ( 'padSeqBool' . ucfirst($padSeqOprName) ) ( $padSeqLoop );
    elseif ( $padSeqOprBld == ''         ) $padSeqOprNow = $padSeqLoop;
    else                                   $padSeqOprNow = include "/pad/sequence/types/$padSeqOprName/$padSeqOprBld.php";

    if     ( $padSeqOprNow === NULL  ) $padSeqOprNow = FALSE; 
    elseif ( $padSeqOprNow === INF   ) $padSeqOprNow = FALSE; 
    elseif ( $padSeqOprNow === NAN   ) $padSeqOprNow = FALSE; 

    if ( $padSeqOprType == 'keep' )  {

      if     ( $padSeqOprNow === TRUE       ) continue;
      elseif ( $padSeqOprNow === FALSE      ) return FALSE;
      elseif ( $padSeqOprNow <> $padSeqLoop ) return FALSE;
      else                                    continue;

    } elseif ( $padSeqOprType == 'remove' ) {

      if     ( $padSeqOprNow === TRUE       ) return FALSE;
      elseif ( $padSeqOprNow === FALSE      ) continue;
      elseif ( $padSeqOprNow == $padSeqLoop ) return FALSE;
      else                                    continue;

    }

    if     ( $padSeqOprNow === FALSE ) return FALSE;
    elseif ( $padSeqOprNow === TRUE  ) $padSeqOprNow = $padSeqLoop;

    $padSeqOprSeq = $padSeqOprNow;

  }

  return $padSeqOprSeq;

?>