<?php

  $pBase [0] .= '{content}';

  $pBuild_now = substr(APP, 0, -1);

  $pBuild_mrg = pExplode ("pages/$page", '/');

  foreach ($pBuild_mrg as $pBuild_key => $pBuild_value) {

    $pBuild_now .= "/$pBuild_value";

    if ( $pBuild_key == array_key_last($pBuild_mrg) 
       and (file_exists("$pBuild_now.php") or file_exists("$pBuild_now.html") ) ) {

      $pBuild_html = pGet_html ( "$pBuild_now.html" );

    } elseif ( is_dir ($pBuild_now) ) {

      $pBuild_inits = pGet_html ( "$pBuild_now/inits.html" );
      $pBuild_exits = pGet_html ( "$pBuild_now/exits.html" );

      if ( strpos($pBuild_inits, '{content}') === FALSE and strpos($pBuild_exits, '{content}') === FALSE  )
        $pBuild_inits .= '{content}';

      if ( $pBuild_merge == 'content' )
        if ( strpos($pBuild_inits, '{content}') !== FALSE )
          $pBuild_html = str_replace ( '{content}', "{content}$pBuild_exits", $pBuild_inits );
        else
          $pBuild_html = str_replace ( '{content}', "$pBuild_inits{content}", $pBuild_exits );
      else
        $pBuild_html = $pBuild_inits . $pBuild_exits ;

    } else {

      $pBuild_html = pGet_html ( "$pBuild_now.html" );

    }

    $pBase [0] = str_replace ( '{content}', $pBuild_html, $pBase [0] );

  }

?>