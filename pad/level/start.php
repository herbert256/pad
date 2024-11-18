<?php

  include PAD . 'level/setup.php';    
  include PAD . 'level/parms/parms.php';    
  include PAD . 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include PAD . "options/else.php";    
  if ( padTagParm ('data') ) $padData [$pad] = include PAD . "options/data.php";   

  include PAD . 'level/set.php';
  include PAD . 'catch/go.php';  
  include PAD . 'level/flags.php';
  include PAD . 'level/base.php';
  include PAD . 'level/data.php';

  if ( count ( $padOptionsAppStart [$pad] ) )
    include PAD . 'options/go/app.php';

  include PAD . 'options/go/start.php';
  include PAD . 'level/name.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') ) 
    include PAD . 'level/start_end/end1.php';

  if ( $GLOBALS ['padInfo'] ) 
    include PAD . 'events/levelStart.php';  

  if ( padTagParm ('dump') )
    include PAD . 'options/dump.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') ) 
    return include PAD . 'level/start_end/start1.php';

  if ( count ( $padData [$pad] ) and $padBase [$pad] ) {
    $padInfoOccur = 'start'; 
    include PAD . 'occurrence/start.php';
  }

?>