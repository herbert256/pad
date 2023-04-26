<?php

  include 'setup.php';    

  if ( ! in_array ( $padTag [$pad], $padPrmNoParse  )  )
    include 'parms.php';
  
  include 'split.php';
  include 'inits.php';

  $padContent = $padTrue [$pad];
  include 'go.php';
  $padTrue [$pad] = $padContent;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  include pad . "options/go/start.php";

  if ( count ( $padOptionsApp [$pad] ) )
    include pad . "options/go/app.php";

  include 'name.php';
  
  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/init.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@', 2 ) )
    include 'split/after1.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@', 1 ) )
    return include 'split/before1.php';

  if ( count ( $padData [$pad] ) )
    include pad . 'occurrence/start.php';

?>