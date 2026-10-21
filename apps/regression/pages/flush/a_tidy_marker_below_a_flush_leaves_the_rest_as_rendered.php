<?php

  // A page that flushed sends the rest as it rendered it: an @tidy@ marker below the {flush}
  // is taken out, and tidy - off once the page flushes - does not run. Tidy made a whole
  // document of the rest, and a second <!DOCTYPE html><html><head> followed the head the
  // flush had sent. Fetched without padInclude, which keeps tidy out on its own.

  $tidyBelow = padCurl ( $padGoExt . 'flush/tidybelow' );

  echo 'doctypes: ', substr_count ( $tidyBelow ['data'], '<!DOCTYPE' ),
       ', marker: ', ( str_contains ( $tidyBelow ['data'], '@tidy@' ) ? 'sent' : 'gone' ),
       ', rest: ', ( str_contains ( $tidyBelow ['data'], '<body><p>after</p></body></html>' ) ? 'as rendered' : 'changed' );

?>
