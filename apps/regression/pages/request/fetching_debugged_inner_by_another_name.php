<?php

  // This site under the other spelling of its host - localhost for 127.0.0.1 - which an
  // application writing a full address can name as easily as the engine's own SELF://.

  $other = ( str_contains ( $padHost, '//127.0.0.1' ) ? str_replace ( '//127.0.0.1', '//localhost', $padHost )
                                                      : str_replace ( '//localhost', '//127.0.0.1', $padHost ) )
         . 'regression/pages/?request/debugged_inner&padInclude';

?>
