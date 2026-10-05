<?php

  // A redirect to ?page - the name written as a link writes it, which {redirect} takes
  // without a check as a target of its own - goes to that page: the ? is the one the
  // address already has, where a second one was put in front of it and the browser was
  // sent to ??page, a page that is not there.

  $rqStay = padCurl ( [ 'url' => $padGoExt . 'request/hopquery&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  echo $rqStay ['result'], ' ', substr ( $rqStay ['info'] ['redirect_url'] ?? '', strlen ( $padHost . $padApp . '/' ) );

?>
