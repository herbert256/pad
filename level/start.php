<?php

  include 'level/setup.php';    
 
  if ( ! in_array ( $padTag [$pad], $padPrmNoParse  )  )
    include 'level/parms.php';

  if ( isset ( $padPrm [$pad] ['isolate'] ) )
    include 'isolate/start.php';
   
  include 'level/split.php';

  if ( padTagParm ('content') ) $padTrue  [$pad] = include "_options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include "_options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include "_options/data.php";   

  include 'level/set.php';

  $padContent = $padTrue [$pad];
  include 'level/go.php';
  $padTrue [$pad] = $padContent;
  
  include 'level/flags.php';
  include 'level/base.php';
  include 'level/data.php';

  include "options/start.php";

  if ( count ( $padOptionsApp [$pad] ) )
    include "options/app.php";

  include 'level/name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') )
    include 'split/after1.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') )
    return include 'split/before1.php';

  if ( count ( $padData [$pad] ) )
    include 'occurrence/start.php';

?>