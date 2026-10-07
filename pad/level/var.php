<?php

  // Renders the variable tag forms - {$x}, {!x}, {#x}, {&x}, {?x}, {^x} - without a new level.
  //
  // Reached from level/level.php through try/try.php ($padTry = 'level/var'). The name runs
  // up to the first '|', anything after it is the pipe expression; a second '$' ({$$x})
  // means one round of indirection. The leading character picks the source: field, raw
  // (unescaped) field, tag option, tag property, url parameter, or the field as JSON for
  // an HTML attribute. A name that does not
  // exist is reported under the strict syntax check - resolved to empty with it off -
  // unless the pipe starts with optional, default or ??. Plain fields additionally run
  // through the $padDataDefaultStart and $padDataDefaultEnd chains from config (sanitize by
  // default), and padLevel() splices the value back into the surrounding text - protected
  // first under $padProtectValues, so the scan it lands in reads it as text.

  $padPipe = strpos ( $padBetween, '|' );

  if ( $padPipe ) {
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padVarOpts = trim(substr($padBetween, $padPipe+1));

    // A pipe with nothing behind it does nothing, silently. Strict mode names the hole.

    if ( $padVarOpts === '' and $padCheckSyntax )
      padError ( "an empty pipe on {" . $padBetween . "}" );

  } else {
    $padFld  = rtrim(substr($padBetween, 1));
    $padVarOpts = '';
  }

  // A {#...} whose name is not name-shaped meant to be a comment and lost its closing # -
  // the {#option} sigil form always carries a plain name.

  if ( $padFirst == '#' and $padCheckSyntax and ! padValidVar ( $padFld ) )
    padError ( "the comment {# ... does not close with #}" );

  // {$$x} takes the name of the field from a value, and a value may come from the request -
  // the name was used unchecked, so a value naming one of the engine's own globals read it
  // out. A value-chosen name must be an application field or a path into one (padValueName,
  // lib/level.php): every part is checked, not only the head, since a wildcard, an ordinal,
  // a prefix or a condition in the path reaches a global the head does not name -
  // *.HTTP_COOKIE read the Cookie header, x:padSqlUser the database user - and markup is
  // refused, as {?$sel} with 'a"><b>' closed its attribute. Every ordinary field name
  // stands - a row field named first-name, _id, café or name@rows that the direct form reads.

  $padFldRefused = FALSE;

  if ( substr($padFld, 0, 1) == '$' ) {

    $padFld = padFieldValue ( substr($padFld, 1) );

    if ( ! is_scalar ( $padFld ) or is_bool ( $padFld ) )
      $padFld = '';

    $padFld        = (string) $padFld;
    $padFldRefused = ! padValueName ( $padFld );

  }

  if     ( $padFldRefused   ) $padFldChk = FALSE;
  elseif ( $padFirst == '$' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '?' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '!' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '#' ) $padFldChk = padOptCheck   ( $padFld );
  elseif ( $padFirst == '&' ) $padFldChk = padTagCheck   ( $padFld );
  elseif ( $padFirst == '^' ) $padFldChk = padJsonCheck  ( $padFld );

  // A name that is not there is reported under the strict syntax check; with the check
  // off it resolves to empty, the same lenient contract expressions keep.

  // A pipe that supplies the value for an empty field - optional, default(...), ?? - is
  // the author saying the field may be missing.

  $padVarFallback = preg_match ( '/^(optional|default\b|\?\?)/', $padVarOpts );

  if ( $padFldRefused and ! $padVarFallback and $padCheckSyntax )
    padError ( "the name '$padFld' that {" . $padBetween . "} takes from a value is no application variable" );

  // A field that holds a list, written where {$x} wants a value, is named as what it is,
  // not reported missing - the field is there - the way the expression form does
  // (lib/eval/after.php): {$items} with $items an array said "Field '$items' not found" and
  // sent the author hunting a typo that was not there.

  if ( ! $padFldRefused and ! $padFldChk and ! $padVarFallback and $padCheckSyntax
       and in_array ( $padFirst, [ '$', '?', '!' ], TRUE ) and padArrayCheck ( $padFld ) )
    padError ( "the field '$padFirst$padFld' is a list, not a value" );

  if ( ! $padFldRefused and ! $padFldChk and ! $padVarFallback and $padCheckSyntax and ! padStrHidden ( $padFld ) )
    padError ( "Field '$padFirst$padFld' not found" );

  if     ( $padFldRefused   ) $padVal = '';
  elseif ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '?' ) $padVal = padUrlValue   ($padFld);
  elseif ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);
  elseif ( $padFirst == '^' ) $padVal = padJsonEscape ($padFld);

  // The field {$x} and the tag parameter {#x} both run the sanitize chain: a parameter is
  // the caller's data, and a component that prints {#title} wrote it to the page raw, so
  // {card title=$x} with '<script>' was an injection. {!x} stays the raw escape hatch, and
  // {?x} {^x} {&x} keep their own encodings.

  if ( in_array ( $padFirst, [ '$', '#' ], TRUE ) )
    foreach ( $padDataDefaultStart as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  if ( $padVarOpts )
    $padVal = padEval ( $padVarOpts, $padVal, TRUE );

  // The markdown pipe answers HTML that is safe by construction - it escaped the raw HTML
  // of the value - so a field whose last pipe it is skips the end chain, whose sanitize
  // would escape the markup just made: {$post.body | markdown} shows the post.

  if ( in_array ( $padFirst, [ '$', '#' ], TRUE ) and ! preg_match ( '/(^|\|)\s*markdown\s*(\(\s*\))?\s*$/', $padVarOpts ) )
    foreach ( $padDataDefaultEnd as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  // Last of all, so the pipes and the sanitize chain work on the value as it really is.
  // Every sigil form: the raw, option and property values are the ones no sanitize ever
  // touched.

  if ( $padProtectValues )
    $padVal = padSpliceQuote ( padProtect ( $padVal ) );

  padLevel ( $padVal );

?>
