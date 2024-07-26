<?php

  include pad . 'level/setup.php';    
  include pad . 'level/parms.php';    
  include pad . 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include pad . "options/else.php";    
  if ( padTagParm ('data') ) $padData [$pad] = include pad . "options/data.php";   

  include pad . 'level/set.php';
  include pad . 'level/go.php';  
  include pad . 'level/flags.php';
  include pad . 'level/base.php';
  include pad . 'level/data.php';

  if ( count ( $padOptionsAppStart [$pad] ) )
    include pad . 'options/go/app.php';

  include pad . 'options/go/start.php';
  include pad . 'level/name.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') ) 
    include pad . 'level/start_end/end1.php';

  if ( padInfo ) 
    include pad . 'info/events/levelStart.php';  

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') ) 
    return include pad . 'level/start_end/start1.php';

  if ( count ( $padData [$pad] ) and $padBase [$pad] )
    include pad . 'occurrence/start.php';

?>