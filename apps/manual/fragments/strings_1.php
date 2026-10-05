<?php

  $text = 'PAD templates drive the flow: data, logic and output all follow the template.';

  $limit   = padStrLimit   ( $text, 20 );
  $words   = padStrWords   ( $text, 4 );
  $excerpt = padStrExcerpt ( $text, 'LOGIC', 10 );
  $squish  = padStrSquish  ( "  data,\n\t logic   and  output " );

?>
