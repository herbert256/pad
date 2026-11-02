<?php

  // A {pad:cache} section that holds a printable with pdf is not kept, as a {pad:pdf} keeps none: the
  // page asked again with &padPdf answers the sheets as a PDF the second time too. Kept, the
  // section came back on the second request without the printable having run, and its links
  // answered the page's HTML.

  $ask    = $padHost . 'regression/framework/?printables/a_printable_with_pdf_in_a_cache_section_keeps_its_links_alive';
  $first  = padCurl ( $ask );
  $pdf    = padCurl ( "$ask&padPdf" );

  $answer = ( str_contains ( $first ['data'], 'class="pad-pdf-links"' ) ? 'the page' : 'something else' )
          . ', then ' . ( $pdf ['headers'] ['Content-Type'] ?? '?' );

?>
