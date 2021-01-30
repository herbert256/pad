<?php

  $pad_html [0] .= '{content}';

  $pad_build_now = $pad_build_base;

  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_now .= "/$pad_value";

    if ( is_dir ($pad_build_now) ) {

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

    $pad_html [0] = str_replace ( '{content}', $pad_build_html, $pad_html [0] );

    pad_trace ('build/html', 'html=' . $pad_build_html );

  }

?>