<?php

  phpinfo ();

  echo ( "<br><hr><pre> ");

  dump ( 'Filters', filter_list ()   );
  dump ( 'HEADERS', getallheaders () );
  dump ( 'SERVER',  $_SERVER         );
  dump ( 'ENV',     $_ENV            ) ;

  $constants = get_defined_constants (1);
  foreach ( get_loaded_extensions () as $extension ) {
    dump ( "$extension - functions", get_extension_funcs ($extension) );
    if ( isset($constants[$extension] ) )
      dump ( "$extension - constants", $constants[$extension] );
  }

  foreach ( get_declared_classes() as $class ) {
    dump ( "$class - Methods",    get_class_methods ($class) );
    if ( count(get_class_vars ($class)) )
      dump ( "$class - Properties", get_class_vars ($class) );
  }
  
  dump ( 'get_declared_interfaces', get_declared_interfaces () );
  dump ( 'get_declared_traits',     get_declared_traits     () );

  function dump ( $txt, $arr ) {

    echo "\n<b>[$txt]</b>\n";

    $padrint = htmlentities ( print_r ( $arr, TRUE ) ) ;

    $padrint = str_replace  (" =&gt; Array\n" ,"\n",   $padrint);
    $padrint = str_replace  (")\n\n",          ")\n" , $padrint);
    $padrint = preg_replace ("/[\n]\s+\(/",    "",     $padrint);
    $padrint = preg_replace ("/[\n]\s+\)/",    "",     $padrint);

    echo substr ( $padrint, 8, strlen($padrint) - 10);

  }

?>