<?php

  phpinfo ();
  
  xdebug_info();  

  echo "<pre>";

  $constants = get_defined_constants (1);
  foreach ( get_loaded_extensions () as $extension ) {
    dump ( "$extension - functions", get_extension_funcs ($extension) );
    if ( isset($constants[$extension] ) )
      dump ( "$extension - constants", $constants[$extension] );
  }

  function dump ( $txt, $arr ) {

    echo "\n<b>[$txt]</b>\n";

    $print = htmlentities ( print_r ( $arr, TRUE ) ) ;

    $print = str_replace  (" =&gt; Array\n" ,"\n",   $print);
    $print = str_replace  (")\n\n",          ")\n" , $print);
    $print = preg_replace ("/[\n]\s+\(/",    "",     $print);
    $print = preg_replace ("/[\n]\s+\)/",    "",     $print);

    echo substr ( $print, 8, strlen($print) - 10);

  }

?>