<?php

  $pad_options = 'occur_end';
  include PAD_HOME . "options/go/options.php";

  if ( $pad_walks [$pad_lvl] == 'occurrence-end' )
    include PAD_HOME . "walk/occurrence-end.php";

  if ( $pad_trace ) 
    include 'trace/end.php';
  
  $pad_result [$pad_lvl] .= $pad_html [$pad_lvl];

  pad_reset ($pad_lvl, $pad_lvl);

?>