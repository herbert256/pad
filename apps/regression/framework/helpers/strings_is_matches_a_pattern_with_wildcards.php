<?php

  $r = json_encode ( [
    padStrIs ( 'admin/*', 'admin/users' ),
    padStrIs ( 'admin/*', 'admin/' ),
    padStrIs ( 'admin/*', 'user/admin' ),
    padStrIs ( '*.pad', 'orders/list.pad' ),
    padStrIs ( '*.pad', "two\nlines.pad" ),
    padStrIs ( 'a*c', 'abbbc' ),
    padStrIs ( 'a.c', 'abc' ),
    padStrIs ( 'Admin*', 'admin' ),
    padStrIs ( [ 'a*', 'b*' ], 'bcd' ),
    padStrIs ( [ 'a*', 'b*' ], 'cde' ),
    padStrIs ( [], 'x' ),
    padStrIs ( '*', '' ),
    padStrIs ( '', '' ),
    padStrIs ( 'é*', 'été' ),
    padStrIs ( '1*', 123 ),
    padStrIs ( '', NULL )
  ] );

?>
