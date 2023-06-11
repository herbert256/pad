<?php

  include pad . 'level/setup.php';    

  $padHistory [] = "Start: {" .  $padBetween . '}';
 
  if ( ! in_array ( $padTag [$pad], $padPrmNoParse  )  )
    include pad . 'level/parms.php';

  if ( isset ( $padPrm [$pad] ['isolate'] ) )
    include pad . 'isolate/start.php';
   
  include pad . 'level/split.php';

  if ( padTagParm ('content') ) $padTrue  [$pad] = include pad . "_options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include pad . "_options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include pad . "_options/data.php";   

  include pad . 'level/set.php';

  $padContent = $padTrue [$pad];
  include pad . 'level/go.php';
  $padTrue [$pad] = $padContent;
  
  include pad . 'level/flags.php';
  include pad . 'level/base.php';
  include pad . 'level/data.php';

 $padHistory [] = "Result: hit=$padHit[$pad] else=$padElse[$pad] array=$padArray[$pad] count=$padCount[$pad] null=$padNull[$pad]" ;

  include pad . "options/start.php";

  if ( count ( $padOptionsApp [$pad] ) )
    include pad . "options/app.php";

  include pad . 'level/name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') )
    include pad . 'level/split/after1.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') )
    return include pad . 'level/split/before1.php';

  if ( count ( $padData [$pad] ) )
    include pad . 'occurrence/start.php';

?>