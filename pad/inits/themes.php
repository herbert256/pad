<?php

  // The post of a {themes/} switcher - the frame's bar holds one on every page - is taken
  // here, before any of the application runs: the choice is kept in the session
  // (padThemeSwitch, subs/themes/_lib/theme.php) and the visitor sent back to the address the
  // form was posted to, as a GET (303). A page's own PHP never sees the post, so a page that
  // acts on any POST - a cart, a guestbook, a logout - does nothing when a theme is chosen on
  // it, and a reload does not post the choice again. After the CSRF check, so with $padCsrf on
  // a post without the session's token ends there; with $padTheme = FALSE, which takes the
  // switcher away, the post goes to the page as any other.

  if ( ( $_POST [padFormName] ?? '' ) === 'padThemes' and padRequestIs ( 'POST' ) and $padTheme !== FALSE ) {
    include_once PAD . 'subs/themes/_lib/theme.php';
    padThemeSwitch ();
    padThemeBack ();
  }

?>
