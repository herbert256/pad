<?php

  // The body of {themes/} - a switcher of the themes of subs/themes/types/: a form that posts the
  // choice back to the page, which keeps it in the session (padThemeSwitch, _lib/theme.php) -
  // every page of the site is in that theme from then on, this one already, as it goes out
  // (padThemeAdd). Default takes the choice away: the application's $padTheme, or the frame's
  // own colours. A <select> and a button, no script: the page changes when the visitor says
  // so, not while an arrow key walks the list (WCAG 3.2.2), and it works without JavaScript.
  // label= is the select's label ('Theme'), button= the button's text ('Apply'). The form
  // writes no token of its own, so showing it starts no session - padThemeSwitch says why;
  // with $padCsrf on it carries the session's token as every form then does. It stays on one
  // line; in the frame's bar of a narrow window its label is there for a screen reader alone,
  // where label, list and button stood above each other and ran out of the bar.

  include_once PAD . 'subs/themes/_lib/theme.php';

  if ( $padPair [$pad] and $padCheckSyntax )
    padError ( "the {themes} takes no content - it is written single, {themes/}" );

  padThemeSwitch ();

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
      . '@media (max-width:800px){.frame-top .pad-themes label{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%)}}'
      . '.pad-themes select,.pad-themes button{font:inherit;padding:5px 10px;border-radius:6px;border:1px solid var(--line,#c9ccd2);background:var(--panel,#fff);color:var(--text,#1d2330)}'
      . '.pad-themes button{cursor:pointer;background:var(--accent,#2563eb);border-color:var(--accent,#2563eb);color:var(--accent-text,#fff)}'
      . '</style>'
    : '';

  return padProtect ( $padThemesStyle
       . '<form method="post" class="pad-themes">'
       . '<input type="hidden" name="' . padFormName . '" value="padThemes">'
       . '<label for="' . $padThemesId . '">' . padWidgetAttr ( $padThemesLabel ) . '</label>'
       . '<select id="' . $padThemesId . '" name="padTheme">' . $padThemesOptions . '</select>'
       . '<button type="submit">' . padWidgetAttr ( $padThemesButton ) . '</button>'
       . '</form>' );

?>
