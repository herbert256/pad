<?php


  /**
   * Validates a general PAD identifier name.
   *
   * Must start with letter, can contain letters, numbers,
   * underscores, colons, and hash.
   *
   * @param string $name The name to validate.
   *
   * @return bool TRUE if valid, FALSE otherwise.
   */
  function padValid ( $name ) {

    if ( trim ( $name ) == '' )
      return FALSE;

    if ( padAtCheck ( $name ) !== INF )
      return TRUE;

    if ( ! preg_match ( '/^[a-zA-Z][:#a-zA-Z0-9_]*$/',$name ) )
      return FALSE;

    return TRUE;

  }


  /**
   * Validates a file path for security.
   *
   * Checks for valid characters, no path traversal (..), and
   * ensures path is within APP, DAT, or PAD directories.
   *
   * @param string $file The file path to validate.
   *
   * @return bool TRUE if valid and safe, FALSE otherwise.
   */
  function padValidFile ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, APP)  ) return TRUE;
    if ( str_starts_with($file, DAT)  ) return TRUE;
    if ( str_starts_with($file, PAD)  ) return TRUE;

    return FALSE;

  }


  /**
   * Validates a variable name for user variables.
   *
   * Must start with letter or underscore, no 'pad' prefix
   * (reserved for framework), alphanumeric with underscores.
   *
   * @param string $name The variable name to validate.
   *
   * @return bool TRUE if valid user variable name.
   */
  function padValidVar ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( substr($name, 0, 3) == 'pad' )                      return FALSE;
    if ( ! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$name) )  return FALSE;

    return TRUE;

  }


  /**
   * Validates an @ field path component.
   *
   * Must start with alphanumeric, underscore, or hyphen.
   * Can contain letters, numbers, underscores, colons.
   *
   * @param string $part The path component to validate.
   *
   * @return bool TRUE if valid @ field component.
   */
  function padAtValid ( $part ) {

    if ( trim($part) == '' )                                       return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z0-9_-][a-zA-Z0-9_:]*$/', $part ) ) return FALSE;

    return TRUE;

  }


  /**
   * Validates a type name.
   *
   * Must be letters only, starting with a letter.
   *
   * @param string $name The type name to validate.
   *
   * @return bool TRUE if valid type name.
   */
  function padValidType ($name) {

    if ( trim($name) == '' )
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z][a-zA-Z]*$/',$name) )
      return FALSE;

    return TRUE;

  }


  /**
   * Validates a tag name.
   *
   * Valid if it's an @ reference or alphanumeric starting
   * with letter, allowing colons and underscores.
   *
   * @param string $name The tag name to validate.
   *
   * @return bool TRUE if valid tag name.
   */
  function padValidTag ($name) {

    if ( trim($name) == '' )
      return FALSE;

    if ( padAtCheck ($name) )
      return TRUE;

    if ( preg_match('/^[a-zA-Z][a-zA-Z0-9:_]*$/',$name) )
      return TRUE;

    return FALSE;

  }


?>