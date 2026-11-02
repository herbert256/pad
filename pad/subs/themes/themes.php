<?php

  // The body of {widget:themes/} - a switcher of the themes of subs/themes/types/: a form that posts the
  // choice back to the page. The post is taken before the application runs (inits/themes.php):
  // the choice kept in the padTheme cookie of the PAD applications (padThemeSwitch,
  // _lib/theme.php) and the visitor sent back to the page as a GET - the pages built on the
  // frame, and every page of an application that sets $padTheme, are in that theme from then
  // on (padThemeAdd). Default takes the choice away: the application's $padTheme, or the
  // frame's own colours. A <select> alone, on one line: choosing a theme applies it at once -
  // a script posts the form when the list changes - and the button is there only without
  // JavaScript, in a <noscript>. label= is the select's label ('Theme'), there for a screen
  // reader alone, button= the text of that button ('Apply'). The form writes no token of its
  // own, so showing it or choosing starts no session - padThemeSwitch says why; with $padCsrf
  // on it carries the session's token as every form then does. The frame of the PAD
  // applications (_common's padFrame) has one in its bar.

  include_once PAD . 'subs/themes/_lib/theme.php';

  if ( $padPair [$pad] and $padCheckSyntax )
    padError ( "the {widget:themes} takes no content - it is written single, {widget:themes/}" );

  $padThemesLabel  = trim ( padParmText ( (string) padTagParm ( 'label', 'Theme' ) ) );
  $padThemesButton = trim ( padParmText ( (string) padTagParm ( 'button', 'Apply' ) ) );
  $padThemesNow    = padThemeChosen ();
  $padThemesId     = padWidgetId ( 'themes', [ $padThemesLabel, $padThemesButton ] );

  $padThemesOptions = '<option value=""' . ( $padThemesNow === '' ? ' selected' : '' ) . '>Default</option>';

  foreach ( padThemeTypes () as $padThemesOne )
    $padThemesOptions .= '<option value="' . $padThemesOne . '"' . ( $padThemesNow === $padThemesOne ? ' selected' : '' ) . '>'
                       . padWidgetAttr ( padThemeData ( $padThemesOne ) ['name'] ) . '</option>';

  $padThemesStyle = padWidgetOnce ( 'style:themes' )
    ? '<style' . padStyleNonce () . '>'
      . '.pad-themes{display:inline-flex;align-items:center;gap:8px;margin:0;white-space:nowrap}'
      . '.pad-themes label{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%)}'
      . '.pad-themes select,.pad-themes button{font:inherit;padding:5px 10px;border-radius:6px;border:1px solid var(--line,#c9ccd2);background:var(--panel,#fff);color:var(--text,#1d2330)}'
      . '.pad-themes button{cursor:pointer;background:var(--accent,#2563eb);border-color:var(--accent,#2563eb);color:var(--accent-text,#fff)}'
      . '</style>'
    : '';

  return padProtect ( $padThemesStyle
       . '<form method="post" class="pad-themes">'
       . '<input type="hidden" name="' . padFormName . '" value="padThemes">'
       . '<label for="' . $padThemesId . '">' . padWidgetAttr ( $padThemesLabel ) . '</label>'
       . '<select id="' . $padThemesId . '" name="padTheme">' . $padThemesOptions . '</select>'
       . '<noscript><button type="submit">' . padWidgetAttr ( $padThemesButton ) . '</button></noscript>'
       . '</form>' )
       . padWidgetScript ( 'themes', 'document.addEventListener("change",function(e){var s=e.target;'
                                   . 'if(s.matches&&s.matches(".pad-themes select")&&s.form)s.form.requestSubmit?s.form.requestSubmit():s.form.submit()})' );

?>
