<?php

  $padBase [$pad] .= '@pad@';
  $padBuildNow     = substr(padApp, 0, -1);  

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( $padBuildKey == array_key_last($padBuildMrg) )

      $padBuildHtml = padGetHtml ( "$padBuildNow.html" );

    elseif ( is_dir ($padBuildNow) ) {

      $padBuildInits = str_replace ( '@content@', '@pad@', padGetHtml ( "$padBuildNow/_inits.html" ) );
      $padBuildExits = str_replace ( '@content@', '@pad@', padGetHtml ( "$padBuildNow/_exits.html" ) );

      if ( strpos($padBuildInits, '@pad@') === FALSE and strpos($padBuildExits, '@pad@') === FALSE  )
        $padBuildInits .= '@pad@';

      if ( $padBuildMerge == 'content' )
        if ( strpos($padBuildInits, '@pad@') !== FALSE )
          $padBuildHtml = str_replace ( '@pad@', "@pad@$padBuildExits", $padBuildInits );
        else
          $padBuildHtml = str_replace ( '@pad@', "$padBuildInits@pad@", $padBuildExits );
      else
        $padBuildHtml = $padBuildInits . $padBuildExits ;

    } else

      $padBuildHtml = padGetHtml ( "$padBuildNow.html" );

    $padBase [$pad] = str_replace ( '@pad@', $padBuildHtml, $padBase [$pad] );

  }

?>