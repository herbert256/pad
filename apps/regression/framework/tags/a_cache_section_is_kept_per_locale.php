<?php

  // With several locales a {cache} section is kept per locale, as a cached page is: the
  // section written for nl was served to en - "Je winkelwagen" for every language.

  $padLocales = [ 'en', 'nl' ];
  $padLocale  = 'nl';

  padFragmentForget ( 'cart-per-locale' );

  function cacheCaseLocale ( $locale ) { $GLOBALS ['padLocale'] = $locale; return ''; }

?>
