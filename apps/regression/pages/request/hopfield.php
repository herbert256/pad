<?php

  // A fixture: {redirect} to a target that holds a field - in a quoted text, or as the {?q}
  // query fragment - run as a template runs it. An action page - the tag ends the request
  // through padRedirect's exit - so the walker leaves it out; request/redirect_with_a_field
  // fetches it deliberately.

  $q = "O'Neil";
  $n = 'a,b';
  $t = 'Tom & Jerry';

  if ( padRequest ( 'form' ) == 'url' )
    echo padCode ( "{redirect '?request/vars{?t}'}" );
  else
    echo padCode ( "{redirect '?request/vars&q={\$q}&n={\$n}'}" );

?>
