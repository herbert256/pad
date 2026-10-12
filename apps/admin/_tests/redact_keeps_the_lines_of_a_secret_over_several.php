<?php

  // A secret over several lines - an XML element, a PHP string: redacted into one line, every
  // line after it was numbered too low in the search and on the files page.

  $linesXml = adminSearchLines ( "<config>\n  <password>\n    hunter2\n  </password>\n  <host>db</host>\n</config>\n", 'host', 0 );
  $linesPhp = adminSearchLines ( "<?php\n  \$secretKey = 'line one\nline two\nline three';\n  \$host = 'db';\n?>", 'host', 0 );

  $linesSaid = 'xml host on line ' . implode ( ',', array_map ( fn ( $at ) => $at + 1, array_keys ( $linesXml ) ) ) . ', php host on line ' . implode ( ',', array_map ( fn ( $at ) => $at + 1, array_keys ( $linesPhp ) ) )
             . ( str_contains ( adminRedact ( "<password>\n  hunter2\n</password>" ), 'hunter2' ) ? ', shown' : ', hidden' );

?>
