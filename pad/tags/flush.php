<?php

  // {flush}: sends the page rendered so far to the browser now, rather than with the rest
  // at the end - lib/flush.php. Put after the wrapper's </head>, it lets the browser load
  // the page's stylesheets while a slow part of the page renders. It stands at the top
  // level of the page or its wrapper; elsewhere strict mode says so. For an output that is
  // no web page - a file, a download, the console - and in a page rendered inside another,
  // it does nothing.

  // A page rendered inside another - {page}, the manual's {example} - flushes nothing: it
  // is not where the response is, and the page around it decides. Inside a tag of its own
  // pass it is a mistake.

  if ( count ( $padLevel ?? [] ) > 1 )
    return '';

  if ( padFlushCan () )
    padFlush ();
  elseif ( $pad != 1 and $padCheckSyntax )
    padError ( "{flush} sends what is above it, so it stands at the top level of the page or its wrapper - not inside another tag, nor in a page whose PHP returns data" );

  return '';

?>