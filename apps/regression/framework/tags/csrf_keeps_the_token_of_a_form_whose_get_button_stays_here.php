<?php

  // A form with a Save button that posts and a Preview button that sends it by GET to this
  // site keeps its token - whatever the formmethod is spelled, "gett" or "" being GET to a
  // browser too: the token in the URL reaches this site's own logs and history only, and
  // without it every Save was answered 403. A GET button to another site is judged by its
  // formaction, as every button is, and takes the token away.

  $padCsrf = TRUE;

?>
