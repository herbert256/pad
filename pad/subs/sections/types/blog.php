<?php

  // {section:blog title='From the blog'} {section:item 'Pages that drive themselves',
  // link='?blog/drive', date='2026-10-30', author='Ada Lovelace', tag='Templates',
  // image='drive.jpg'}What changes when the template decides.{/section:item} ...
  // {/section:blog} - the latest articles, a card each: a picture over it, its title as the
  // link to it - the whole card a target for the pointer, one link for the keyboard and a
  // screen reader - its date, its tag, a few lines from it and who wrote it. The articles are
  // the rows of data= - their fields title, link, date, author, tag, image, alt and text (the
  // lines from it) - or {section:item} pairs or single tags, the item's first value the title
  // and its content the lines. A date written 2026-10-30 is a <time> a machine reads too, any
  // other date stands as it is. The picture decorates - the title says what the card is - so
  // its alt text is empty unless alt= gives one. In the page the title comes first, then the
  // date, the lines and the author, the picture drawn above them all. columns= as for features
  // (3).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'title', [ 'title', 'link', 'date', 'author', 'tag', 'image', 'alt', 'text' ], 'text', 'articles' );
  $padSecCols  = padSectionInt ( 'columns', max ( 1, min ( 3, count ( $padSecItems ) ) ), 1, 6 );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecCards = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecTitle  = padSectionField ( $padSecItem, 'title' );
    $padSecLink   = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecText   = padSectionField ( $padSecItem, 'text' );
    $padSecAuthor = padSectionField ( $padSecItem, 'author' );
    $padSecTag    = padSectionField ( $padSecItem, 'tag' );
    $padSecImage  = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecDate   = trim ( (string) ( is_scalar ( padSectionRaw ( $padSecItem, 'date' ) ) ? padSectionRaw ( $padSecItem, 'date' ) : '' ) );

    if ( $padSecTitle === '' ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "an article of the " . padSectionWhat () . " has no title - the item's first value, or the field title of its row" );
      continue;
    }

    if ( $padSecLink !== '' )
      $padSecTitle = "<a class=\"ps-post-link\" href=\"$padSecLink\">$padSecTitle</a>";

    if ( $padSecDate !== '' )
      $padSecDate = preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $padSecDate )
                  ? "<time datetime=\"$padSecDate\">$padSecDate</time>"
                  : padSectionEscape ( $padSecDate );

    $padSecMeta = ( $padSecTag  !== '' ? "<span class=\"ps-post-tag\">$padSecTag</span>" : '' )
                . ( $padSecDate !== '' ? "<span class=\"ps-post-date\">$padSecDate</span>" : '' );

    $padSecCards .= '<li class="ps-card ps-post">'
                  . "<h$padSecLevel class=\"ps-item-title\">$padSecTitle</h$padSecLevel>"
                  . ( $padSecMeta   !== '' ? "<p class=\"ps-post-meta\">$padSecMeta</p>" : '' )
                  . ( $padSecText   !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                  . ( $padSecAuthor !== '' ? "<p class=\"ps-post-author\">$padSecAuthor</p>" : '' )
                  . ( $padSecImage  !== '' ? "<div class=\"ps-post-image\"><img src=\"$padSecImage\" alt=\"" . padSectionField ( $padSecItem, 'alt' ) . '" loading="lazy"></div>' : '' )
                  . '</li>';

  }

  return padSection ( 'blog', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-posts\">$padSecCards</ul>" );

?>
