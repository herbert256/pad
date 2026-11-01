<?php

  // The browser leaves a disabled select out of the post, and its padLists[], written disabled
  // with it, too: form f came back - the other field to be corrected - with neither, so the
  // locked choices show value= again, as the disabled text field beside it does. The
  // padLists[] written enabled went along alone, and the choices came back emptied.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'other' => '' ];

?>
