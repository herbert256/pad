<?php

  include 'setup.php';    

  if ( ! in_array ( $padTag [$pad], $padPrmNoParse  )  )
    include 'parms.php';
  
  include 'split.php';

  if ( padTagParm ('content') ) $padTrue  [$pad] = include pad . "options/content.php";    
  if ( padTagParm ('else')    ) $padFalse [$pad] = include pad . "options/else.php";    
  if ( padTagParm ('data')    ) $padData  [$pad] = include pad . "options/data.php";   

  $padContent = $padTrue [$pad];
  include 'go.php';
  $padTrue [$pad] = $padContent;
  
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  include pad . "options/go/start.php";

  if ( count ( $padOptionsApp [$pad] ) )
    include pad . "options/go/app.php";

  include 'name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') )
    include 'split/after1.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') )
    return include 'split/before1.php';

  if ( count ( $padData [$pad] ) )
    include pad . 'occurrence/start.php';

?>