<?php

  // A page holding a token its request made - the session's CSRF token, which every {form}
  // writes, the CSP nonce, PAD's session or request id - is not recorded: the replay gets a
  // new one, and the case was reported changed on every replay, accepted or not. The page
  // beside it, the same in every request, is.

  $tokenStore = 'fw' . padRandomString ( 8 );

  padCurl ( $padHost . "regression/framework/?develop/a_page_holding_the_session_s_csrf_token&padInclude&padRecord=$tokenStore" );
  padCurl ( $padHost . "regression/framework/?develop/a_page_whose_answer_is_replayed&padInclude&padRecord=$tokenStore" );

  $tokenCases = padReplayCases ( $tokenStore );

  padReplayDelete ( $tokenStore );

  $tokenRecorded = implode ( ', ', array_map ( fn ( $case ) => $case ['query'], $tokenCases ) );

?>
