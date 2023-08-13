<?php

  if ( padExists ( "$padSeqType/init.php" )) 
    include "$padSeqType/init.php";
      
  if     ( $padSeqPull  and $padSeqSeq <> 'pull'  ) $padSeqFor = $padSeqStore [$padSeqPull];
  elseif ( $padSeqRange and $padSeqSeq <> 'range' ) $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );
  elseif ( $padSeqList  and $padSeqSeq <> 'list'  ) $padSeqFor = padGetList ( $padSeqList );

  if ( count ($padSeqFor) )
    include pad . "sequence/type/for.php";
  else
    include pad . "sequence/type/$padSeqBuild.php";

  include pad . 'sequence/type/actions.php';
 
  if ( padExists ( "$padSeqType/exit.php" ) )   
    include "$padSeqType/exit.php";    

?>