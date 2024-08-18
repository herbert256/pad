<?php

  include '/pad/seq/inits/inits.php';
      
  if     ( $padSeqPull  ) $padSeqFor = $padSeqStore [$padSeqPull];
  elseif ( $padSeqRange ) $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );
  elseif ( $padSeqList  ) $padSeqFor = padGetList ( $padSeqList );

  if ( $GLOBALS ['padInfo'] ) 
    include '/pad/events/seqStart.php'; 

  if ( $padSeqFor !== FALSE )
    include "/pad/seq/builds/for.php";
  else
    include "/pad/seq/builds/$padSeqBuild.php";

  include '/pad/seq/actions.php';
 
  if ( file_exists ( "$padSeqType/exit.php" ) )   
    include "$padSeqType/exit.php";    

  include '/pad/seq/exits/push.php';
  include '/pad/seq/exits/return.php';

  return $padSeqReturn;

?>