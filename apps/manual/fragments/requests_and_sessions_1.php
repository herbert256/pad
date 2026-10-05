<?php

  // As if a form had been posted to ?orders&sort=date with these
  // values.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_GET  ['sort'] = 'date';
  $_POST = [ 'customer' => [ 'name'  => ' Ann ',
                             'email' => 'ann@example.com' ],
             'note'     => '',
             'password' => 'secret' ];

  $name   = padRequest ( 'customer.name' );
  $sort   = padRequest ( 'sort', 'number' );
  $pageNo = padRequest ( 'page', 1 );
  $note   = padRequestHas    ( 'note' ) ? 'sent'   : 'not sent';
  $filled = padRequestFilled ( 'note' ) ? 'filled' : 'blank';
  $only   = padRequestOnly   ( 'customer.email, note' );
  $saved  = json_encode ( $only );
  $method = padRequestMethod ();
  $posted = padRequestIs ( 'post' ) ? 'yes' : 'no';

?>
