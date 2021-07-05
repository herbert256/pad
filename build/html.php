<?php

  $pad_base [1] .= '{content}';

  $pad_build_now = $pad_build_base;

  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_build_key => $pad_build_value) {

    $pad_build_now .= "/$pad_build_value";

    if ( $pad_build_key == array_key_last($pad_build_mrg) 
       and (pad_file_exists("$pad_build_now.php") or pad_file_exists("$pad_build_now.html") ) ) {

      $pad_build_html = pad_get_html ( "$pad_build_now.html" );

    } elseif ( is_dir ($pad_build_now) ) {

      $pad_build_inits = pad_get_html ( "$pad_build_now/inits.html" );
      $pad_build_exits = pad_get_html ( "$pad_build_now/exits.html" );

      if ( strpos($pad_build_inits, '{content}') === FALSE and strpos($pad_build_exits, '{content}') === FALSE  )
        $pad_build_inits .= '{content}';

      if ( $pad_build_merge == 'content' )
        if ( strpos($pad_build_inits, '{content}') !== FALSE )
          $pad_build_html = str_replace ( '{content}', "{content}$pad_build_exits", $pad_build_inits );
        else
          $pad_build_html = str_replace ( '{content}', "$pad_build_inits{content}", $pad_build_exits );
      else
        $pad_build_html = $pad_build_inits . $pad_build_exits ;

    } else {

      $pad_build_html = pad_get_html ( "$pad_build_now.html" );

    }

    $pad_base [1] = str_replace ( '{content}', $pad_build_html, $pad_base [1] );

  }

?>