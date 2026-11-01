<?php

  // The feeds - RSS 2.0, Atom (RFC 4287) and JSON Feed 1.1 - the writers of types/rss.php,
  // types/atom.php and types/jsonfeed.php. A row is an entry, its fields picked by the names
  // they go by - the front matter of a Markdown collection works as it is:
  //
  //   title     title, name
  //   link      link, url - else, for a row with a slug, the feed's link= with the slug
  //             behind it: link='https://example.com/blog/' gives .../blog/hello
  //   date      date, published, updated, created, pubDate - any date PHP reads
  //   summary   summary, description, excerpt
  //   content   body, content, html - HTML, a collection's body
  //   id        id, uid, guid - else the link, else a urn:uuid made of the title and date
  //   author    author
  //
  // The feed's own options: title= (else 'Feed'), link= (the site), description=, author=,
  // language=, self= (the feed's own address), id=. Its updated is the newest entry's date -
  // never the clock: the same rows give the same bytes - and with no dates at all
  // 1970-01-01T00:00:00Z, or updated= when given. The entries are written in the order of
  // the rows; sort them with the handling options where they come from.

  function padFeedEntries ( $rows, $options ) {

    $entries = [];
    $base    = (string) ( $options ['link'] ?? '' );

    foreach ( $rows as $row ) {

      $link = padFormatPick ( $row, [ 'link', 'url', 'href' ] );
      $slug = padFormatPick ( $row, [ 'slug' ] );

      if ( $link === '' and $slug !== '' and $base !== '' )
        $link = $base . rawurlencode ( $slug );

      $date  = padFormatDate ( padFormatPickValue ( $row, [ 'date', 'published', 'updated', 'created', 'pubDate', 'start' ] ) );
      $title = padFormatPick ( $row, [ 'title', 'name', 'summary' ] );

      $entries [] = [
        'title'   => $title,
        'link'    => $link,
        'date'    => $date ? $date [0]->setTimezone ( new DateTimeZone ( 'UTC' ) ) : NULL,
        'summary' => padFormatPick ( $row, [ 'summary', 'description', 'excerpt' ] ),
        'content' => padFormatPick ( $row, [ 'body', 'content', 'html', 'content_html' ] ),
        'id'      => padFormatPick ( $row, [ 'id', 'uid', 'guid' ] ) ?: $link,
        'author'  => padFormatPick ( $row, [ 'author', 'creator' ] )
      ];

    }

    return $entries;

  }

  // The feed's updated: the updated= option, else the newest entry's date, else the epoch.

  function padFeedUpdated ( $entries, $options ) {

    if ( ( $options ['updated'] ?? '' ) !== '' and ( $date = padFormatDate ( $options ['updated'] ) ) )
      return $date [0]->setTimezone ( new DateTimeZone ( 'UTC' ) );

    $newest = NULL;

    foreach ( $entries as $entry )
      if ( $entry ['date'] and ( $newest === NULL or $entry ['date'] > $newest ) )
        $newest = $entry ['date'];

    return $newest ?? new DateTimeImmutable ( '@0' );

  }

  function padFeedId ( $entry ) {

    if ( $entry ['id'] !== '' and preg_match ( '/^[a-z][a-z0-9+.-]*:/i', $entry ['id'] ) )
      return $entry ['id'];

    return 'urn:uuid:' . padFormatUuid ( $entry ['id'] . '|' . $entry ['title'] . '|' . ( $entry ['date'] ? $entry ['date']->format ( 'U' ) : '' ) );

  }

  // RSS 2.0: a channel with its title, link and description - the three it must have - and an
  // item per row; the date as RFC 822 has it, the summary - or else the content - as the
  // item's description, the id as its guid, a permalink when it is the item's link.

  function padRssWrite ( $rows, $options ) {

    $entries = padFeedEntries ( $rows, $options );
    $x       = 'padFormatXml';
    $title   = (string) ( $options ['title'] ?? 'Feed' );
    $self    = (string) ( $options ['self'] ?? '' );

    $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
         . '<rss version="2.0"' . ( $self !== '' ? ' xmlns:atom="http://www.w3.org/2005/Atom"' : '' ) . ">\n"
         . "  <channel>\n"
         . '    <title>' . $x ( $title ) . "</title>\n"
         . '    <link>' . $x ( $options ['link'] ?? '' ) . "</link>\n"
         . '    <description>' . $x ( $options ['description'] ?? $title ) . "</description>\n";

    if ( $self !== '' )
      $out .= '    <atom:link href="' . $x ( $self ) . "\" rel=\"self\" type=\"application/rss+xml\"/>\n";

    if ( ( $options ['language'] ?? '' ) !== '' )
      $out .= '    <language>' . $x ( $options ['language'] ) . "</language>\n";

    $out .= '    <lastBuildDate>' . padFeedUpdated ( $entries, $options )->format ( DATE_RSS ) . "</lastBuildDate>\n";

    foreach ( $entries as $entry ) {

      $out  .= "    <item>\n";
      $guid  = $entry ['id'] !== '' ? $entry ['id'] : padFeedId ( $entry );

      // An item has a title or a description (RSS 2.0): one with neither is titled by its id.

      if ( $entry ['title'] === '' and ( $entry ['summary'] ?: $entry ['content'] ) === '' )
        $entry ['title'] = $guid;

      if ( $entry ['title'] !== '' )
        $out .= '      <title>' . $x ( $entry ['title'] ) . "</title>\n";

      if ( $entry ['link'] !== '' )
        $out .= '      <link>' . $x ( $entry ['link'] ) . "</link>\n";

      if ( ( $entry ['summary'] ?: $entry ['content'] ) !== '' )
        $out .= '      <description>' . $x ( $entry ['summary'] ?: $entry ['content'] ) . "</description>\n";

      if ( $entry ['author'] !== '' and str_contains ( $entry ['author'], '@' ) )
        $out .= '      <author>' . $x ( $entry ['author'] ) . "</author>\n";

      if ( $entry ['date'] )
        $out .= '      <pubDate>' . $entry ['date']->format ( DATE_RSS ) . "</pubDate>\n";

      $perma = ( $guid === $entry ['link'] and $guid !== '' ) ? 'true' : 'false';

      $out .= "      <guid isPermaLink=\"$perma\">" . $x ( $guid ) . "</guid>\n"
            . "    </item>\n";

    }

    return $out . "  </channel>\n</rss>\n";

  }

  // Atom (RFC 4287): the feed's id, title and updated, an author - the feed's, else its
  // title, as every entry needs one - and per entry its id, title and updated (the feed's
  // when it has no date of its own), its link, its summary as text and its content as HTML.

  function padAtomWrite ( $rows, $options ) {

    $entries = padFeedEntries ( $rows, $options );
    $x       = 'padFormatXml';
    $title   = (string) ( $options ['title'] ?? 'Feed' );
    $updated = padFeedUpdated ( $entries, $options );
    $link    = (string) ( $options ['link'] ?? '' );
    $self    = (string) ( $options ['self'] ?? '' );
    $id      = (string) ( $options ['id'] ?? '' ) ?: $link ?: $self ?: 'urn:uuid:' . padFormatUuid ( $title );

    $out = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
         . "<feed xmlns=\"http://www.w3.org/2005/Atom\"" . ( ( $options ['language'] ?? '' ) !== '' ? ' xml:lang="' . $x ( $options ['language'] ) . '"' : '' ) . ">\n"
         . '  <title>' . $x ( $title ) . "</title>\n";

    if ( ( $options ['description'] ?? '' ) !== '' )
      $out .= '  <subtitle>' . $x ( $options ['description'] ) . "</subtitle>\n";

    if ( $link !== '' )
      $out .= '  <link href="' . $x ( $link ) . "\"/>\n";

    if ( $self !== '' )
      $out .= '  <link rel="self" href="' . $x ( $self ) . "\"/>\n";

    $out .= '  <updated>' . padFeedTime ( $updated ) . "</updated>\n"
          . '  <author>' . "\n" . '    <name>' . $x ( ( $options ['author'] ?? '' ) !== '' ? $options ['author'] : $title ) . "</name>\n  </author>\n"
          . '  <id>' . $x ( $id ) . "</id>\n";

    foreach ( $entries as $entry ) {

      $out .= "  <entry>\n"
            . '    <title>' . $x ( $entry ['title'] ) . "</title>\n";

      if ( $entry ['link'] !== '' )
        $out .= '    <link href="' . $x ( $entry ['link'] ) . "\"/>\n";

      $out .= '    <id>' . $x ( padFeedId ( $entry ) ) . "</id>\n"
            . '    <updated>' . padFeedTime ( $entry ['date'] ?? $updated ) . "</updated>\n";

      if ( $entry ['author'] !== '' )
        $out .= "    <author>\n      <name>" . $x ( $entry ['author'] ) . "</name>\n    </author>\n";

      if ( $entry ['summary'] !== '' )
        $out .= '    <summary>' . $x ( $entry ['summary'] ) . "</summary>\n";

      if ( $entry ['content'] !== '' )
        $out .= '    <content type="html">' . $x ( $entry ['content'] ) . "</content>\n";

      $out .= "  </entry>\n";

    }

    return $out . "</feed>\n";

  }

  function padFeedTime ( $moment ) {

    return $moment->format ( 'Y-m-d\TH:i:s\Z' );

  }

  // JSON Feed 1.1: version, title, home_page_url, feed_url, description, authors, language
  // and the items - id (required), url, title, content_html (the content) or content_text
  // (the summary, else the title: an item has one of the two), summary, date_published.

  function padJsonFeedWrite ( $rows, $options ) {

    $entries = padFeedEntries ( $rows, $options );

    $feed = [ 'version' => 'https://jsonfeed.org/version/1.1', 'title' => (string) ( $options ['title'] ?? 'Feed' ) ];

    foreach ( [ 'home_page_url' => 'link', 'feed_url' => 'self', 'description' => 'description', 'language' => 'language' ] as $key => $option )
      if ( ( $options [$option] ?? '' ) !== '' )
        $feed [$key] = (string) $options [$option];

    if ( ( $options ['author'] ?? '' ) !== '' )
      $feed ['authors'] = [ [ 'name' => (string) $options ['author'] ] ];

    $feed ['items'] = [];

    foreach ( $entries as $entry ) {

      $item = [ 'id' => $entry ['id'] !== '' ? $entry ['id'] : padFeedId ( $entry ) ];

      if ( $entry ['link'] !== '' )
        $item ['url'] = $entry ['link'];

      if ( $entry ['title'] !== '' )
        $item ['title'] = $entry ['title'];

      if ( $entry ['content'] !== '' )
        $item ['content_html'] = $entry ['content'];
      else
        $item ['content_text'] = $entry ['summary'] ?: $entry ['title'];

      if ( $entry ['summary'] !== '' )
        $item ['summary'] = $entry ['summary'];

      if ( $entry ['date'] )
        $item ['date_published'] = padFeedTime ( $entry ['date'] );

      if ( $entry ['author'] !== '' )
        $item ['authors'] = [ [ 'name' => $entry ['author'] ] ];

      $feed ['items'] [] = $item;

    }

    return json_encode ( $feed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n";

  }

?>
