<?php

  include 'level/setup.php';    
  include 'level/parms/parms.php';    
  include 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include "options/else.php";    
  if ( padTagParm ('data') ) $padData [$pad] = include "options/data.php";   

  include 'level/set.php';
  include 'catch/go.php';  
  include 'level/flags.php';
  include 'level/base.php';
  include 'level/data.php';

  if ( count ( $padOptionsAppStart [$pad] ) )
    include 'options/go/app.php';

  include 'options/go/start.php';
  include 'level/name.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') ) 
    include 'level/start_end/end1.php';

  if ( $GLOBALS ['padInfo'] ) 
    include 'events/levelStart.php';  

  if ( padTagParm ('dump') )
    include 'options/dump.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') ) 
    return include 'level/start_end/start1.php';

  if ( count ( $padData [$pad] ) and $padBase [$pad] ) {
    $padInfoOccur = 'start'; 
    include 'occurrence/start.php';
  }

?>