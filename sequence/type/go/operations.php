<?php

  $padSeqOperations     = $padSequence;
  $padSeqOperationsLast = $padSequence;

  foreach ( $padSeqOpr as $padSeqOprName => $padSeqOprValue ) {

    if ( in_array($padSeqOprName, $padSeqOneDone ) )
      continue;

    $padSeqChk = "$padSeqTypes/$padSeqOprName";

    $padSeqParmSave = $padSeqParm;

    padSeqSet ( $padSeqOprName, $padSeqOprValue );

    $padSeqLoop = $padSeqOperations;

    if     ( in_array ( $padSeqOprName , $padSeqSpecialOps ) ) $padSeqOperations = include pad . "sequence/type/go/list.php";
    elseif ( padExists ( "$padSeqChk/make.php" )             ) $padSeqOperations = include "$padSeqChk/make.php";
    elseif ( padExists ( "$padSeqChk/filter.php" )           ) $padSeqOperations = include "$padSeqChk/filter.php";

    $padSeqParm = $padSeqParmSave;

    if     ( $padSeqOperations === FALSE ) return TRUE;   
    elseif ( $padSeqOperations === NULL  ) return FALSE;
    elseif ( $padSeqOperations === INF   ) return FALSE; 
    elseif ( $padSeqOperations === NAN   ) return FALSE; 
    elseif ( $padSeqOperations === TRUE  ) $padSeqOperations = $padSeqOperationsLast;     

    $padSeqOperationsLast = $padSeqOperations;
   
  }

  return $padSeqOperations;

?>