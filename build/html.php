<?php

  $padBase [$pad] .= '{content}';
  $padBuildNow     = substr(APP, 0, -1);  

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( $padBuildKey == array_key_last($padBuildMrg) 
       and (file_exists("$padBuildNow.php") or file_exists("$padBuildNow.html") ) ) {

      $padBuildHtml = padGetHtml ( "$padBuildNow.html" );

    } elseif ( is_dir ($padBuildNow) ) {

      $padBuildInits = padGetHtml ( "$padBuildNow/inits.html" );
      $padBuildExits = padGetHtml ( "$padBuildNow/exits.html" );

      if ( strpos($padBuildInits, '{content}') === FALSE and strpos($padBuildExits, '{content}') === FALSE  )
        $padBuildInits .= '{content}';

      if ( $padBuildMerge == 'content' )
        if ( strpos($padBuildInits, '{content}') !== FALSE )
          $padBuildHtml = str_replace ( '{content}', "{content}$padBuildExits", $padBuildInits );
        else
          $padBuildHtml = str_replace ( '{content}', "$padBuildInits{content}", $padBuildExits );
      else
        $padBuildHtml = $padBuildInits . $padBuildExits ;

    } else {

      $padBuildHtml = padGetHtml ( "$padBuildNow.html" );

    }

    $padBase [$pad] = str_replace ( '{content}', $padBuildHtml, $padBase [$pad] );

  }

?>