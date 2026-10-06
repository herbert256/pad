<?php

  // The fixture of request/form_rules_on_names_php_rewrites_post: two fields whose names PHP
  // does not keep as they are - user[email] arrives as $_POST ['user'] ['email'] and
  // first.name as $_POST ['first_name'] - each with rules of its own.

  $personState = padPosted ( 'person' )
               ? 'stored ' . padRequest ( 'user.email' ) . ' ' . padRequest ( 'first_name' )
               : 'not stored';

?>
