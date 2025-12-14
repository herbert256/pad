<?php


  /**
   * Merges new content into true/false branches based on condition.
   *
   * Splits new content at {else} tag and merges appropriate part
   * into the corresponding branch.
   *
   * @param string &$true     The true branch content.
   * @param string &$false    The false branch content.
   * @param string $new       New content to merge (may contain {else}).
   * @param bool   $condition Which branch to merge into.
   *
   * @return void
   */
  function padContentMerge ( &$true, &$false, $new, $condition ) {

    padContentElse ( $new, $newTrue, $newFalse ) ;

    if ( $condition ) $true  = padContentSet ( $true,  $newTrue );
    else              $false = padContentSet ( $false, $newFalse );

  }


  /**
   * Combines base and new content using @content@ or merge mode.
   *
   * If either content has @content@ placeholder, uses that for
   * positioning. Otherwise uses merge option (top/bottom/replace).
   *
   * @param string $base The existing base content.
   * @param string $new  The new content to merge.
   *
   * @return string The combined content.
   */
  function padContentSet ( $base, $new ) {

    $check = padContentBeforeAfter ( $new,  $before, $after );

    if ( $check )
      return $before . $base . $after;

    $check = padContentBeforeAfter ( $base,  $before, $after );

    if ( $check )
      return $before . $new . $after;

    $merge = padTagParm ( 'merge', 'top' );

    if     ( $merge == 'bottom'  ) return $base . $new;
    elseif ( $merge == 'top'     ) return $new . $base;
    elseif ( $merge == 'replace' ) return $new;

  }


  /**
   * Splits content at top-level {else} tag.
   *
   * Finds {else} that isn't nested inside other tags and splits
   * content into before and after parts.
   *
   * @param string $input   The content to split.
   * @param string &$before Output: Content before {else}.
   * @param string &$after  Output: Content after {else}.
   *
   * @return void
   */
  function padContentElse ( $input, &$before, &$after ) {

    $list = padOpenCloseList ( $input ) ;
    $pos  = strpos ( $input, '{else}' );

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCount ( substr ( $input, 0, $pos ), $list ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+6  );
        return;
      }

      $pos = strpos ( $input, '{else}', $pos+1 );

    }

    $before = $input;
    $after  = '';

  }


  /**
   * Splits content at top-level @content@ placeholder.
   *
   * Finds @content@ that isn't nested inside {content} tags
   * and returns the before/after parts.
   *
   * @param string $input   The content to split.
   * @param string &$before Output: Content before @content@.
   * @param string &$after  Output: Content after @content@.
   *
   * @return bool TRUE if @content@ was found, FALSE otherwise.
   */
  function padContentBeforeAfter ( $input, &$before, &$after ) {

    $pos = strpos ( $input, '@content@' );

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCountOne ( substr ( $input, 0, $pos ), 'content' ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+9  );
        return TRUE;
      }

      $pos = strpos ( $input, '@content@', $pos+1 );

    }

    return FALSE;

  }


?>