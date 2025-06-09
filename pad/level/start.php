<?php

  include 'level/setup.php';    
  
  include 'level/parms/parms.php';    
  include 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include "options/else.php";    
  if ( padTagParm ('data') ) $padData [$pad] = include "options/data.php";   

  include 'level/set.php';
  
  $padTry = 'level/go';
  include 'try/try.php';

  include 'level/base.php';
  include 'level/data.php';
  include 'level/name.php';
  include 'handling/handling.php';  

  if ( padTagParm ('dump') )
    include 'options/dump.php';

  if ( count ( $padOptionsAppStart [$pad] ) )
    include 'options/go/app.php';

  include 'options/go/start.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') ) 
    include 'level/start_end/end1.php';

  if ( $padInfo ) 
    include 'events/levelStart.php';  

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') ) 
    return include 'level/start_end/start1.php';

  if ( count ( $padData [$pad] ) and $padBase [$pad] )
    include 'occurrence/occurence.php';

?>