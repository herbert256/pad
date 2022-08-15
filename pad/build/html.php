<?php

  $padBase [$pad] .= '{content}';

  $padBuild_now = substr(APP, 0, -1);

  $padBuild_mrg = padExplode ("pages/$page", '/');

  foreach ($padBuild_mrg as $padBuild_key => $padBuild_value) {

    $padBuild_now .= "/$padBuild_value";

    if ( $padBuild_key == array_key_last($padBuild_mrg) 
       and (file_exists("$padBuild_now.php") or file_exists("$padBuild_now.html") ) ) {

      $padBuild_html = padGetHtml ( "$padBuild_now.html" );

    } elseif ( is_dir ($padBuild_now) ) {

      $padBuild_inits = padGetHtml ( "$padBuild_now/inits.html" );
      $padBuild_exits = padGetHtml ( "$padBuild_now/exits.html" );

      if ( strpos($padBuild_inits, '{content}') === FALSE and strpos($padBuild_exits, '{content}') === FALSE  )
        $padBuild_inits .= '{content}';

      if ( $padBuild_merge == 'content' )
        if ( strpos($padBuild_inits, '{content}') !== FALSE )
          $padBuild_html = str_replace ( '{content}', "{content}$padBuild_exits", $padBuild_inits );
        else
          $padBuild_html = str_replace ( '{content}', "$padBuild_inits{content}", $padBuild_exits );
      else
        $padBuild_html = $padBuild_inits . $padBuild_exits ;

    } else {

      $padBuild_html = padGetHtml ( "$padBuild_now.html" );

    }

    $padBase [$pad] = str_replace ( '{content}', $padBuild_html, $padBase [$pad] );

  }

?>