<?php

  include pad . 'sequence/inits/inits.php';
      
  if     ( $padSeqPull  ) $padSeqFor = $padSeqStore [$padSeqPull];
  elseif ( $padSeqRange ) $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );
  elseif ( $padSeqList  ) $padSeqFor = padGetList ( $padSeqList );

  if ( $padXref )
    include pad . 'tail/types/xref/items/sequence.php'; 

  if ( $padSeqFor !== FALSE )
    include pad . "sequence/builds/for.php";
  else
    include pad . "sequence/builds/$padSeqBuild.php";

  include pad . 'sequence/actions.php';
 
  if ( padExists ( "$padSeqType/exit.php" ) )   
    include "$padSeqType/exit.php";    

  include pad . 'sequence/exits/exits.php';
 
  return $padSeqReturn;

?>