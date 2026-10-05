<?php

  // {markdown} ... {/markdown}: the content is rendered first, like any other - its fields
  // and tags resolve - and the result is then read as Markdown and written as HTML by
  // padMarkdown in lib/markdown.php. Like {tidy} it runs twice: first to ask for the end
  // walk, then on the rendered content. {markdown $text} renders the value of its parameter
  // instead, the way the markdown pipe does.
  //
  // Raw HTML in the text is escaped; the html option lets the author's own markup through.
  // A value that took part - a field, a tag's answer - travels protected through the
  // rendering (values are text), and the ignore option turns the syntax characters of the
  // source into &open;-style entities. Both are undone for the Markdown reading - a quote
  // in a raw field is escaped inside a link like any other, the = under a setext heading
  // is an = - and the HTML that comes out is protected as a whole, since it joins the page
  // text that is scanned for tags next. Braces in the source - code samples - need the
  // ignore option: {markdown ignore}.

  $padMdHtml = (bool) padTagParm ( 'html' );

  if ( ! $padPair [$pad] ) {

    if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
      padError ( "the pair {markdown} never closes" );

    return padMarkdown ( $padParm, $padMdHtml );

  }

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  if ( $padProtectValues )
    $padContent = padProtect ( padMarkdown ( padUnescape ( padUnprotect ( $padContent ) ), $padMdHtml ) );
  else
    $padContent = padMarkdown ( $padContent, $padMdHtml );

  return TRUE;

?>
