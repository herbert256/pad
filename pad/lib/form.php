<?php

  // Form helpers: validation in the page's PHP, and form fields in the template that refill
  // themselves from what was posted and show the error that validation found for them.
  //
  // padPosted      whether this request posted - and, given a name, posted the {form} of
  //                that name, so a page with two forms knows which one came back
  // padValidate    checks the posted values against rules ('required|email|max:2000') and
  //                answers the errors, one message per field; {input} and {textarea} show
  //                them beside their field
  // padFormValue   what a field shows: the posted value when its form came back, else
  //                the default the template gave
  // padFormError   the message padValidate left for a field, when its form came back
  //
  // The tags - {form}, {input}, {textarea} - are pad/tags/form.php, input.php and
  // textarea.php, built on padFormOpen, padFormInput and padFormTextarea below.
  //
  // Before this every page validated by hand into an $errors array and copied each posted
  // value into a variable of its own to put it back into the form - the demo's contact page
  // did both for four fields.

  const padFormName = 'padForm';

  const padValidateRules = [ 'required', 'accepted', 'email', 'url', 'numeric', 'integer',
                             'min', 'max', 'in', 'regex', 'same', 'date' ];

  function padPosted ( $form = '' ) {

    if ( strtoupper ( $_SERVER ['REQUEST_METHOD'] ?? '' ) != 'POST' )
      return FALSE;

    if ( $form === '' or $form === NULL )
      return TRUE;

    return ( $_POST [padFormName] ?? '' ) === (string) $form;

  }

  // The rules of a field are a string split on | or an array of them; a rule takes its
  // argument after a colon. A field that is empty is checked for 'required' only - any
  // other rule speaks about a value that is there. The first rule a field fails gives its
  // message, which $messages can replace per field ('email') or per field and rule
  // ('email.required'); :label is the field's name made readable, :n the rule's argument.

  function padValidate ( $rules, $data = NULL, $messages = [] ) {

    global $padFormErrors, $padFormErrorParts;

    $padFormErrorParts = [];

    if ( $data === NULL )
      $data = padPosted () ? $_POST : $_GET;

    $errors = [];

    foreach ( $rules as $field => $list ) {

      $list   = is_array ( $list ) ? $list : explode ( '|', (string) $list );
      $list   = array_values ( array_filter ( array_map ( 'trim', $list ), 'strlen' ) );
      $value  = $data [$field] ?? '';
      $value  = is_string ( $value ) ? trim ( $value ) : $value;
      $number = (bool) array_intersect ( $list, [ 'numeric', 'integer' ] );
      $empty  = ( $value === '' or $value === NULL or $value === [] );

      foreach ( $list as $rule )
        if ( ! in_array ( strtolower ( explode ( ':', $rule, 2 ) [0] ), padValidateRules, TRUE ) )
          padValidateUnknown ( $rule );

      foreach ( $list as $rule ) {

        $parts = explode ( ':', $rule, 2 );
        $name  = strtolower ( $parts [0] );
        $arg   = $parts [1] ?? '';

        if ( $empty and $name != 'required' and $name != 'accepted' )
          continue;

        if ( padValidateRule ( $name, $arg, $value, $data, $number ) )
          continue;

        $padFormErrorParts [$field] = padValidateMessage ( $field, $name, $arg, $number, $messages );

        $errors [$field] = padValidateText ( $padFormErrorParts [$field], padValidateName ( $field ) );

        break;

      }

    }

    $padFormErrors = $errors;

    return $errors;

  }

  function padValidateRule ( $name, $arg, $value, $data, $number ) {

    if ( is_array ( $value ) )
      return ( $name == 'required' ) ? count ( $value ) > 0 : TRUE;

    $value = (string) $value;
    $size  = $number ? (float) $value : mb_strlen ( $value );

    return match ( $name ) {
      'required' => $value !== '',
      'accepted' => in_array ( strtolower ( $value ), [ '1', 'on', 'yes', 'true' ], TRUE ),
      'email'    => filter_var ( $value, FILTER_VALIDATE_EMAIL ) !== FALSE,
      'url'      => filter_var ( $value, FILTER_VALIDATE_URL ) !== FALSE && preg_match ( '#^https?://#i', $value ),
      'numeric'  => is_numeric ( $value ),
      'integer'  => filter_var ( $value, FILTER_VALIDATE_INT ) !== FALSE,
      'min'      => $size >= (float) $arg,
      'max'      => $size <= (float) $arg,
      'in'       => in_array ( $value, array_map ( 'trim', explode ( ',', $arg ) ), TRUE ),
      'regex'    => (bool) @preg_match ( $arg, $value ),
      'same'     => $value === trim ( (string) ( $data [$arg] ?? '' ) ),
      'date'     => padValidateDate ( $value ),
      default    => TRUE
    };

  }

  function padValidateDate ( $value ) {

    $date = date_parse ( $value );

    return $date ['error_count'] == 0 and $date ['year'] !== FALSE and $date ['month'] !== FALSE and $date ['day'] !== FALSE
       and checkdate ( $date ['month'], $date ['day'], $date ['year'] );

  }

  // A misspelled rule would pass every value, silently - it is an error, under strict mode
  // and without it, since a rule that checks nothing is a hole in the form. Every rule of
  // a field is looked at before any is applied, so it is named whatever the value.

  function padValidateUnknown ( $rule ) {

    padError ( "padValidate has no rule named '" . explode ( ':', $rule, 2 ) [0] . "'" );

  }

  // The message of a failed rule, kept in two parts - the text with its :label still in
  // it, and the argument - so a field rendered with a label= says its own label: the
  // label E-mail makes 'E-mail is required' of what the field name alone made 'Email is
  // required'.

  function padValidateMessage ( $field, $rule, $arg, $number, $messages ) {

    $text = $messages ["$field.$rule"] ?? $messages [$field] ?? match ( $rule ) {
      'required' => ':label is required',
      'accepted' => ':label must be accepted',
      'email'    => ':label must be a valid e-mail address',
      'url'      => ':label must be a valid web address',
      'numeric'  => ':label must be a number',
      'integer'  => ':label must be a whole number',
      'min'      => $number ? ':label must be at least :n' : ':label must be at least :n characters',
      'max'      => $number ? ':label must be at most :n'  : ':label must be at most :n characters',
      'in'       => ':label must be one of :n',
      'same'     => ':label must be the same as :n',
      'date'     => ':label must be a date',
      default    => ':label is not valid'
    };

    if ( $rule == 'same' )
      $arg = padValidateName ( $arg );
    elseif ( $rule == 'in' )
      $arg = implode ( ', ', array_map ( 'trim', explode ( ',', $arg ) ) );

    return [ $text, $arg ];

  }

  function padValidateText ( $parts, $label ) {

    return strtr ( $parts [0], [ ':label' => $label, ':n' => $parts [1] ] );

  }

  // A field name made readable - first_name is First name - and a label made fit to stand
  // in a sentence: 'Your name: *' is Your name.

  function padValidateName ( $field ) {

    return ucfirst ( trim ( str_replace ( [ '_', '-' ], ' ', (string) $field ) ) );

  }

  function padValidateLabel ( $label ) {

    return rtrim ( trim ( (string) $label ), ' :*' );

  }

  // The form a field stands in: the innermost {form} being rendered, '' outside one.

  function padFormCurrent () {

    global $padFormStack;

    return $padFormStack ? end ( $padFormStack ) ['name'] : '';

  }

  // Where a field's value comes back from, or NULL when its form did not come back: the
  // post when this request posted - the field's own {form}, when it stands in a named one -
  // and for a {form method='get'} the query string once it holds more than the page's
  // name and the engine's own switches: a search box keeps what was asked.

  function padFormSource () {

    global $padFormStack;

    $form = $padFormStack ? end ( $padFormStack ) : NULL;

    if ( $form and $form ['method'] == 'get' ) {
      $asked = array_filter ( array_slice ( array_keys ( $_GET ), 1 ), fn ( $key ) => ! padEngineName ( (string) $key ) );
      return $asked ? $_GET : NULL;
    }

    return padPosted ( $form ['name'] ?? '' ) ? $_POST : NULL;

  }

  function padFormValue ( $name, $default = '' ) {

    $source = padFormSource ();

    if ( $source === NULL or ! array_key_exists ( $name, $source ) )
      return $default;

    return is_string ( $source [$name] ) ? $source [$name] : $default;

  }

  // The error shows where its form came back - beside the fields of the form that posted,
  // and not beside a field of the same name in another form of the page.

  function padFormError ( $name, $label = '' ) {

    global $padFormErrors, $padFormErrorParts;

    if ( ! isset ( $padFormErrors [$name] ) or padFormSource () === NULL )
      return '';

    if ( $label !== '' and isset ( $padFormErrorParts [$name] ) )
      return padValidateText ( $padFormErrorParts [$name], padValidateLabel ( $label ) );

    return (string) $padFormErrors [$name];

  }

  function padFormEscape ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES, 'UTF-8' );

  }

  // The items of {form}, {input} and {textarea}, read raw as {attrs} reads them: the first
  // item without a name that is not a bare word is the name; the names in $special are
  // taken out, evaluated, for the tag to use; everything else is an HTML attribute,
  // written the way {attrs} writes it - a bare word is a bare attribute, required.

  function padFormItems ( $tag, $special ) {

    global $padCheckSyntax;

    $name  = NULL;
    $take  = [];
    $attrs = [];

    foreach ( padAttrsItems () as [ $key, $expr ] ) {

      $bare = preg_match ( '/^[A-Za-z_:@][-A-Za-z0-9_:.@]*$/', $expr );

      if ( $key === '' and ! $bare and $name === NULL ) {
        $name = (string) padEval ( $expr );
        continue;
      }

      if ( $key === '' and $bare ) {
        if ( in_array ( strtolower ( $expr ), $special ) )
          $take [ strtolower ( $expr ) ] = TRUE;
        else
          padAttrsOne ( $attrs, $expr, TRUE );
        continue;
      }

      if ( $key === '' ) {
        $value = padEval ( $expr );
        if ( is_array ( $value ) )
          foreach ( $value as $one => $val )
            padAttrsOne ( $attrs, (string) $one, $val );
        continue;
      }

      if ( in_array ( strtolower ( $key ), $special ) )
        $take [ strtolower ( $key ) ] = padEval ( $expr );
      else
        padAttrsOne ( $attrs, $key, padEval ( $expr ) );

    }

    if ( $tag != 'form' and ( $name === NULL or $name === '' ) and $padCheckSyntax )
      padError ( "the {" . $tag . "} needs a field name - {" . $tag . " 'email'}" );

    return [ (string) $name, $take, $attrs ];

  }

  // The id of a field: the id= item, or the name with what an id cannot hold made a dash.

  function padFormId ( $name, $take ) {

    if ( isset ( $take ['id'] ) and $take ['id'] !== '' )
      return (string) $take ['id'];

    return trim ( preg_replace ( '/[^A-Za-z0-9_-]+/', '-', $name ), '-' );

  }

  // The label before the field, and after it the error with the aria attributes that tie
  // the two together - a screen reader says the message with the field.

  function padFormField ( $name, $take, $attrs, $control ) {

    $id    = padFormId ( $name, $take );
    $label = isset ( $take ['label'] ) ? '<label for="' . padFormEscape ( $id ) . '">' . padFormEscape ( $take ['label'] ) . '</label>' : '';
    $error = padFormError ( $name, (string) ( $take ['label'] ?? '' ) );

    if ( $error !== '' ) {
      $attrs ['aria-invalid']     = 'aria-invalid="true"';
      $attrs ['aria-describedby'] = 'aria-describedby="' . padFormEscape ( "$id-error" ) . '"';
      $error = '<span class="error" id="' . padFormEscape ( "$id-error" ) . '">' . padFormEscape ( $error ) . '</span>';
    }

    $head = 'name="' . padFormEscape ( $name ) . '"' . ( $id !== '' ? ' id="' . padFormEscape ( $id ) . '"' : '' );
    $rest = $attrs ? ' ' . implode ( ' ', $attrs ) : '';

    return $control ( $head, $rest, $label, $error );

  }

  // {input 'email', type='email', label='E-mail', required}. A checkbox or radio is checked
  // when the posted value is its own value - before a post, when checked is given - and has
  // its label after it. A password or file field is never refilled: the one would put a
  // password into the page, the other cannot be. A button has no label and no error.

  function padFormInput () {

    [ $name, $take, $attrs ] = padFormItems ( 'input', [ 'type', 'label', 'value', 'id', 'checked' ] );

    $type    = strtolower ( (string) ( $take ['type'] ?? 'text' ) );
    $default = (string) ( $take ['value'] ?? '' );

    return padFormField ( $name, $take, $attrs, function ( $head, $rest, $label, $error ) use ( $name, $type, $default, $take ) {

      $typed = 'type="' . padFormEscape ( $type ) . '" ' . $head;

      if ( in_array ( $type, [ 'checkbox', 'radio' ] ) ) {

        $own     = ( $default === '' ) ? 'on' : $default;
        $checked = padFormSource () !== NULL
                 ? ( padFormValue ( $name, NULL ) === $own )
                 : padAttrsTrue ( $take ['checked'] ?? FALSE );

        return "<input $typed value=\"" . padFormEscape ( $own ) . '"' . ( $checked ? ' checked' : '' ) . "$rest>$label$error";

      }

      if ( in_array ( $type, [ 'submit', 'button', 'reset', 'image' ] ) )
        return "<input $typed" . ( $default !== '' ? ' value="' . padFormEscape ( $default ) . '"' : '' ) . "$rest>";

      if ( in_array ( $type, [ 'password', 'file' ] ) )
        return "$label<input $typed$rest>$error";

      $value = padFormValue ( $name, $default );

      if ( $type == 'hidden' )
        return "<input $typed value=\"" . padFormEscape ( $value ) . "\"$rest>";

      return "$label<input $typed value=\"" . padFormEscape ( $value ) . "\"$rest>$error";

    } );

  }

  // {textarea 'message', label='Message', rows=6, required}.

  function padFormTextarea () {

    [ $name, $take, $attrs ] = padFormItems ( 'textarea', [ 'label', 'value', 'id' ] );

    $value = padFormValue ( $name, (string) ( $take ['value'] ?? '' ) );

    return padFormField ( $name, $take, $attrs, function ( $head, $rest, $label, $error ) use ( $value ) {

      return "$label<textarea $head$rest>" . padFormEscape ( $value ) . "</textarea>$error";

    } );

  }

  // {form 'contact'} ... {/form}: the opening tag, posting by default, and for a post the
  // session's CSRF token and the form's name, which padPosted ( 'contact' ) reads back. The
  // form is kept on a stack while its content renders, so a field knows which form it is
  // in and refills only when that one came back; padFormClose takes it off again and puts
  // the tags round the rendered content - as values, inert like any other a tag answers.

  function padFormOpen () {

    global $padFormStack;

    [ $name, $take, $attrs ] = padFormItems ( 'form', [ 'method' ] );

    $method = strtolower ( (string) ( $take ['method'] ?? 'post' ) );
    $open   = '<form method="' . padFormEscape ( $method ) . '"' . ( $attrs ? ' ' . implode ( ' ', $attrs ) : '' ) . '>';

    if ( $method == 'post' ) {
      $open .= padCsrfField ();
      if ( $name !== '' )
        $open .= '<input type="hidden" name="' . padFormName . '" value="' . padFormEscape ( $name ) . '">';
    }

    $padFormStack [] = [ 'name' => $name, 'method' => $method, 'open' => $open ];

  }

  function padFormClose ( $content ) {

    global $padFormStack;

    $form = array_pop ( $padFormStack );

    return padProtect ( $form ['open'] ?? '<form>' ) . $content . padProtect ( '</form>' );

  }

?>
