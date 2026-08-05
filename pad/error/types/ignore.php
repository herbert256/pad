<?php

  // $padErrorAction 'ignore': swallow errors entirely. The handlers of error/error.php stay
  // installed so PHP itself prints nothing, but padErrorGo records nothing and just returns
  // TRUE, letting the request run on as if the error had not happened.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    return TRUE;

  }

?>