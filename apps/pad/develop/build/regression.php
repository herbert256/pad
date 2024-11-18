<?php

  deleteDir  ( APP . '_xref' ); 
  deleteDir  ( APP . '_regression');

  foreach ( padList ( 0 ) as $one )
    getPage ( $one ['item'], 1, 1 );
  
  padPageGet ( 'develop/regression/go'  );

  padRedirect ( 'develop/regression'    );

?>