<?php

  // Has is presence - an emptied field was sent - and Filled is content; both ask for every
  // name given, as an array or a comma-separated text. Only keeps the names asked for, in
  // that order, where their dot paths put them, leaving out what was not sent; Except
  // removes names and dot paths.

  $_POST = [ 'name'  => 'Ann', 'note' => '   ', 'zero' => '0', 'list' => [],
             'user'  => [ 'name' => 'Bob', 'mail' => 'bob@example.com' ] ];

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  $h = implode ( ' ', [ $yes ( padRequestHas ( 'note' ) ), $yes ( padRequestHas ( 'name, note' ) ),
                        $yes ( padRequestHas ( [ 'name', 'nope' ] ) ), $yes ( padRequestHas ( 'user.mail' ) ),
                        $yes ( padRequestHas ( '' ) ), $yes ( padRequestHas ( NULL ) ) ] );

  $f = implode ( ' ', [ $yes ( padRequestFilled ( 'name' ) ), $yes ( padRequestFilled ( 'note' ) ),
                        $yes ( padRequestFilled ( 'zero' ) ), $yes ( padRequestFilled ( 'list' ) ),
                        $yes ( padRequestFilled ( 'nope' ) ), $yes ( padRequestFilled ( 'name,zero' ) ) ] );

  $o = json_encode ( padRequestOnly ( 'user.mail, name, nope' ) );
  $e = json_encode ( padRequestExcept ( [ 'padInclude', 'list', 'user.mail', 'nope.deeper' ] ) );

?>
