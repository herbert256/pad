<?php

  // A value in a {cache} section that holds the text of a nonce marker stays that text on a
  // hit - the marker is sealed: the fixed one, brought in by a value, took each later
  // request's real nonce, and a script injected with it passed the policy.

  $markerText = "\u{E0A0}padNonce\u{E0A0}";

  padFragmentForget ( 'nonce-mark' );

?>
