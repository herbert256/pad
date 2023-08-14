<?php

  include pad . 'sequence/inits/inits.php';

  if ( padExists ( "$padSeqType/init.php" )) 
    include "$padSeqType/init.php";
      
  if     ( $padSeqPull  and $padSeqSeq <> 'pull'  ) $padSeqFor = $padSeqStore [$padSeqPull];
  elseif ( $padSeqRange and $padSeqSeq <> 'range' ) $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );
  elseif ( $padSeqList  and $padSeqSeq <> 'list'  ) $padSeqFor = padGetList ( $padSeqList );

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