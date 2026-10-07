<?php

  // A secret field's value ended at a ;, a # or a space, so the rest of it stayed in clear -
  // password=se;cret kept ;cret, a raw password=my secret kept " secret" - while a form body
  // is split at & alone. And only the last key of a bracketed name was read: password[]
  // and user[password][0] kept their values.

  $shown = [
    padRedactQuery ( 'a=1&password=se;cret&b=2' ),
    padRedactQuery ( 'a=1&password=my secret' ),
    padRedactQuery ( 'password[]=hunter2&user[password][0]=x&user[name]=bob' ),
    padRedactText  ( '/x/?reset&token=ab;cd' ),
    padRedactText  ( '<a href="?page&token=abc#top">go</a>' )
  ];

  $result = implode ( ' | ', $shown );

?>
