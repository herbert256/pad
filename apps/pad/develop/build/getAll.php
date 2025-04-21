<?php

  deleteDir  ( APP . '_xref'       ); 
  deleteDir  ( APP . '_regression' );

  foreach ( padList ( 0 ) as $one ) {
    file_put_contents  ( APP . "_getAll.txt", $one ['item'] );
    getPage ( $one ['item'], 1, 1 );
  }
  
  padRedirect ( 'develop/regression'    );

?>