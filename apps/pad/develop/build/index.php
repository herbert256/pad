<?php

  set_time_limit ( 300 );

  include APP . 'develop/build/sequence.php';
  include APP . 'develop/build/getAll.php';

  padReqression ( 0 );
  padReqression ( 1 );

  padRedirect ( 'develop/regression'    );

?>