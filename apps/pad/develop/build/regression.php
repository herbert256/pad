<?php

  deleteDir  ( APP . '_xref'       ); 
  deleteDir  ( APP . '_regression' );

  foreach ( padList ( 0 ) as $one )
    getPage ( $one ['item'], 1, 1 );
  
  padReqression ( 0 );
  padReqression ( 1 );

  padRedirect ( 'develop/regression'    );

?>