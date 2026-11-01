<?php

  // {section:faq title='heading'} {section:item 'Is it free?'}The answer{/section:item} ...
  // {/section:faq} - the questions people ask, each a <details> whose <summary> is the
  // question: the browser opens and closes them, no script. The questions are the rows of
  // data= - their fields question and answer - or {section:item} pairs, the item's first value
  // the question and its content the answer. open= opens one at first, by its number from 1,
  // or every one, written bare; single lets one stand open at a time (the details share a
  // name, which a browser that knows exclusive details honours).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, open,
  // single, data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead   = padSectionHead ();
  $padSecItems  = padSectionItems ( 'question', [ 'question', 'answer' ], 'answer', 'questions' );
  $padSecSingle = padSectionFlag ( 'single' );
  $padSecOpen   = padTagParm ( 'open', 0 );
  $padSecId     = padSectionId ();

  // open= is a number from 1 - the question open at first - or bare, every question.

  if ( $padSecOpen === TRUE )
    $padSecOpen = 'all';
  elseif ( is_string ( $padSecOpen ) and preg_match ( '/^\d+$/D', trim ( padParmText ( $padSecOpen ) ) ) )
    $padSecOpen = (int) trim ( padParmText ( $padSecOpen ) );

  if ( ! ( $padSecOpen === 'all' or ( is_int ( $padSecOpen ) and $padSecOpen >= 0 and $padSecOpen <= count ( $padSecItems ) ) ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padSectionWhat () . " has no open '" . padMakeSafe ( is_scalar ( $padSecOpen ) ? (string) $padSecOpen : '', 20 ) . "' - the number of a question, 1 to " . count ( $padSecItems ) . ", or open alone for all of them" );
    $padSecOpen = 0;
  }

  $padSecList = '';

  foreach ( $padSecItems as $padSecN => $padSecItem ) {

    $padSecAnswer = padSectionField ( $padSecItem, 'answer' );
    $padSecIsOpen = ( $padSecOpen === 'all' or $padSecOpen === $padSecN + 1 );

    $padSecList .= '<details class="ps-faq-item"' . ( $padSecSingle ? " name=\"$padSecId\"" : '' ) . ( $padSecIsOpen ? ' open' : '' ) . '>'
                 . '<summary>' . padSectionField ( $padSecItem, 'question' ) . '</summary>'
                 . padSectionTextBlock ( $padSecAnswer, 'ps-faq-answer' )
                 . '</details>';

  }

  return padSection ( 'faq', $padSecHead . "<div class=\"ps-faq\">$padSecList</div>" );

?>
