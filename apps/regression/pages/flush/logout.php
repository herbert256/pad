<?php

  // A logout below a {flush} while the browser holds a remember-me cookie: the cookie
  // cannot be dropped once the headers have gone out, and the logout answered TRUE - the
  // next request was logged in again by it. padLogout refuses it now.

  $_COOKIE [ padAuthCookieName () ] = 'kept by the browser';

?>
