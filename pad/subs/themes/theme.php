<?php

  // The body of {theme 'paper'/} - the <style> of a theme of subs/themes/types/, written where
  // the tag stands, with the request's nonce: the page is in that theme, whatever $padTheme or
  // a visitor's choice put into its <head>, which the tag's style, standing after it,
  // outweighs (_lib/theme.php).
  //
  // As a pair - {theme 'terminal'}...{/theme} - the theme colours its content alone: a box
  // with the theme's background and text, its properties set on the box instead of the page,
  // its style once per page - a preview of a theme beside another. The content renders first,
  // the end walk puts it in the box.

  include_once PAD . 'subs/themes/_lib/theme.php';

  $padThemeName = is_string ( $padParm ) ? strtolower ( trim ( padParmText ( $padParm ) ) ) : '';

  if ( ! padThemeType ( $padThemeName ) ) {
    if ( $padCheckSyntax and $padWalk [$pad] == 'start' )
      padError ( ( $padThemeName === '' ? "the {theme} needs the name of a theme - {theme 'paper'/}: " : "there is no theme named '" . padMakeSafe ( $padThemeName, 20 ) . "' - " )
                 . implode ( ', ', padThemeTypes () ) );
    return $padPair [$pad] ? TRUE : '';
  }

  if ( ( $padInfoXref ?? FALSE ) and $padWalk [$pad] == 'start' )
    padInfoXref ( 'theme', $padThemeName );

  if ( ! $padPair [$pad] )
    return padProtect ( padThemeStyle ( $padThemeName ) );

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = padProtect ( ( padWidgetOnce ( "style:theme-$padThemeName" ) ? padThemeStyle ( $padThemeName, TRUE ) : '' )
                             . "<div class=\"pad-theme pad-theme-$padThemeName\">" )
              . $padContent
              . padProtect ( '</div>' );

  return TRUE;

?>
