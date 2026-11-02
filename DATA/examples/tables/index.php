<?php

  $title = 'Tables and spreadsheets';

  $sheets = [];

  foreach ( sheetsExamples () as $sheetsName => [ $sheetsTitle, $sheetsLead ] )
    $sheets [] = [ 'page' => $sheetsName, 'name' => $sheetsTitle, 'line' => $sheetsLead ];

  $functions = count ( padKinds ( 'tables/formulas' ) );

?>
