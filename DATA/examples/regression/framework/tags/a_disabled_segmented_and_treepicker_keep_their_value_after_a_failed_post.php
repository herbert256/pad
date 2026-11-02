<?php

  // The browser leaves a disabled radio group out of the post, and its padLists[], written
  // disabled with it, too: form f came back - the other field to be corrected - with
  // neither, so the locked choices show value= again, where a group the post left out was
  // always taken for one sent without a choice and showed none.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'other' => '' ];

?>
