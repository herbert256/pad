<?php

  // _errors/418.pad names a field there is none of: the error page fails, and the answer
  // is the plain line it would have been without one - no loop, no error report.

  padAbort ( 418, 'I am a teapot' );

?>
