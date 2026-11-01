<?php

  // {section:logos text='Trusted by teams at'} {section:item 'Acme', image='acme.svg'/} ...
  // {/section:logos} - the names that vouch for it, in a row that wraps: a logo each, quiet in
  // grey until the pointer is on it. The logos are the rows of data= - their fields name,
  // image and link - or {section:item} pairs or single tags, the item's first value the name
  // (the picture's alt text) and its content the logo itself, an <svg> written in place; a
  // logo without a picture is its name in letters.
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, data,
  // level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'name', [ 'name', 'image', 'link' ], 'logo', 'logos' );
  $padSecList  = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecName  = padSectionField ( $padSecItem, 'name' );
    $padSecImage = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecLink  = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );

    if ( isset ( $padSecItem ['html'] ['logo'] ) )
      $padSecLogo = padSectionField ( $padSecItem, 'logo' ) . ( $padSecName !== '' ? "<span class=\"ps-sr\">$padSecName</span>" : '' );
    elseif ( $padSecImage !== '' )
      $padSecLogo = "<img src=\"$padSecImage\" alt=\"$padSecName\" loading=\"lazy\">";
    else
      $padSecLogo = "<span class=\"ps-logo-name\">$padSecName</span>";

    if ( $padSecLink !== '' )
      $padSecLogo = "<a href=\"$padSecLink\">$padSecLogo</a>";

    $padSecList .= "<li class=\"ps-logo\">$padSecLogo</li>";

  }

  return padSection ( 'logos', $padSecHead . "<ul class=\"ps-logos\">$padSecList</ul>" );

?>
