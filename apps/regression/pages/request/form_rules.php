<?php

  // The fixture of request/form_rules_post: the rules stand on the fields of the template,
  // and this file - running after them - sees only whether the post passed. Its own check
  // of the note adds to the template's errors.

  $own   = padFormCameBack ( 'order' ) ? padValidate ( [ 'note' => 'max:3' ] ) : [];
  $state = ( padPosted ( 'order' ) and ! $own ) ? 'processed ' . padRequest ( 'qty' ) : 'not processed';

  $state .= ' - posted: '  . ( padPosted ()       ? 'yes' : 'no' )
          . ', failed: '   . ( padFormFailed ()   ? 'yes' : 'no' )
          . ', own: '      . count ( $own );

?>
