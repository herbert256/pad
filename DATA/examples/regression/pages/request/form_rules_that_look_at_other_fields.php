<?php

  // The fixture of request/form_rules_that_look_at_other_fields_post: rules on the fields of
  // the template that look at another field of the post - the nested user[address] and
  // user[email] among them, which PHP files as $_POST ['user'] - and not_in, checked before
  // this file runs.

  $tripState = padPosted ( 'trip' ) ? 'stored ' . padRequest ( 'login' ) : 'not stored';

?>
