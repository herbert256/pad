<?php

  // The fixture of request/form_rules_rich_fields_post: the rich fields of pad/subs/inputs/ carry
  // their rules on the template as {input} does, and this file - running after the post pass
  // checked them - sees only whether the post kept them.

  $state = padPosted ( 'trip' ) ? 'stored ' . padRequest ( 'city' ) . ' on ' . padRequest ( 'day' ) : 'not stored';

?>
