<?php

  // mdeditor - Markdown written beside its preview: a native <textarea>, which posts the
  // Markdown as it is, and where the script runs a bar of buttons - bold, italic, a heading,
  // a link, a list, a quote, code - and the text as it will read, kept in step as it is
  // typed. Ctrl or Cmd with B, I and K make bold, italic and a link of the selection. The
  // preview is the script's own reading of the common Markdown - headings, emphasis, code,
  // links, lists, quotes, rules - with every tag of the text written as text and a link only
  // to http, https, mailto or the site itself; {markdown} renders the stored text on the
  // server. rows= the textarea's height, 8 unless given; the text before a post is value= or
  // the content between the tags, as written.

  $padInputRows = padInputNumber ( 'rows', 8, 2, 60, TRUE );

  $padInputNow ['value'] = padFormValue ( padInputName (), (string) padInputTake ( 'value', padInputSource () ) );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputRows ) {

    global $padInputNow;

    $own  = padInputEscape ( $id );
    $mine = padInputOwn ( $rest, [ 'spellcheck' => 'true' ] );

    // A line break follows the opening tag, as {textarea} writes it: the HTML parser drops
    // one right after <textarea>, and a text that began with a blank line lost it.

    return padInputFrame ( 'mdeditor', $worded,
             $label
           . '<div class="pi-md-bar" role="toolbar" aria-label="' . padInputEscape ( "Formatting of $worded" ) . "\" aria-controls=\"$own\" hidden></div>"
           . '<div class="pi-md-panes">'
           . "<textarea $head rows=\"$padInputRows\"$mine$rest>\n" . padInputEscape ( $padInputNow ['value'] ) . '</textarea>'
           . "<div class=\"pi-md-preview\" role=\"region\" aria-label=\"" . padInputEscape ( "Preview of $worded" ) . '" hidden></div>'
           . '</div>',
             $error );

  } );

?>
