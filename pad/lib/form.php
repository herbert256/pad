<?php

  // Form helpers: validation in the page's PHP, and form fields in the template that refill
  // themselves from what was posted and show the error that validation found for them.
  //
  // padPosted      whether this request posted - and, given a name, posted the {form} of
  //                that name, so a page with two forms knows which one came back - and
  //                passed the rules its template gives the form's fields
  // padFormFailed  whether the post came back and broke those rules
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
  // Rules in the template. A field can carry its own rules - {input 'email', rules=
  // 'required|email'} - and the template then says what the page's PHP said with
  // padValidate. The template renders after the PHP, so the rules are not checked while it
  // renders: build/page.php reads them from the page's text before any _inits.php runs
  // (padFormRulesOf, literal values only, the way {meta} is read) and a post of a named
  // {form} is validated there (padFormPost). The page's PHP finds it done: padPosted
  // ( 'contact' ) is TRUE only for a post that passed, so the PHP that stores and redirects
  // runs for a good post alone, and a post that failed renders the form again, refilled,
  // with the messages beside the fields and the error= of the {form} above them. Only the
  // page's own text is read - with its _inits.pad and _exits.pad - so a field with rules
  // in a snippet, a custom tag, a {page} or a layout is an error (padFormRulesCheck), as is
  // one in a form that posts elsewhere (action=), by GET, or a file field.
  //
  // Before this every page validated by hand into an $errors array and copied each posted
  // value into a variable of its own to put it back into the form - the demo's contact page
  // did both for four fields.

  const padFormName = 'padForm';

  const padValidateRules = [ 'required', 'accepted', 'email', 'url', 'numeric', 'integer',
                             'min', 'max', 'in', 'regex', 'same', 'date' ];

  // Asked without a name, on a page whose template gives fields rules, only a post of a
  // form of that template kept them: a post naming no form - padForm left out - or a form
  // the template does not have was never checked, and leaving padForm out of a post skipped
  // every rule of a page that asked padPosted ().

  function padPosted ( $form = '' ) {

    if ( ! padFormCameBack ( $form ) or padFormFailed ( $form ) )
      return FALSE;

    if ( $form !== '' and $form !== NULL )
      return TRUE;

    $rules  = padFormRules ();
    $posted = $_POST [padFormName] ?? '';

    return ! array_filter ( $rules ) or ( is_string ( $posted ) and array_key_exists ( $posted, $rules ) );

  }

  // Whether the form came back, whatever its rules said - what the fields read to refill
  // themselves and to show their errors.

  function padFormCameBack ( $form = '' ) {

    if ( ! padRequestIs ( 'POST' ) )
      return FALSE;

    if ( $form === '' or $form === NULL )
      return TRUE;

    return ( $_POST [padFormName] ?? '' ) === (string) $form;

  }

  // Whether the form came back and broke the rules of its template; without a name, the
  // form the post names.

  function padFormFailed ( $form = '' ) {

    global $padFormChecked;

    if ( ! padFormCameBack ( $form ) )
      return FALSE;

    $posted = $_POST [padFormName] ?? '';

    return is_string ( $posted ) and ! empty ( $padFormChecked [$posted] );

  }

  // The rules of a field are a string split on | or an array of them; a rule takes its
  // argument after a colon. A field that is empty is checked for 'required' and 'accepted'
  // only - any other rule speaks about a value that is there. The first rule a field fails
  // gives its message, which $messages can replace per field ('email') or per field and
  // rule ('email.required'); :label is the field's name made readable, :n the rule's argument.
  //
  // The errors it answers are those of its own fields. The fields show those together with
  // what an earlier check left for other fields - the rules of the template, checked before
  // the page's PHP - so a page can add a check of its own to them.

  function padValidate ( $rules, $data = NULL, $messages = [] ) {

    global $padFormErrors, $padFormErrorParts;

    $padFormErrors     = $padFormErrors     ?? [];
    $padFormErrorParts = $padFormErrorParts ?? [];

    foreach ( array_keys ( $rules ) as $field )
      unset ( $padFormErrors [$field], $padFormErrorParts [$field] );

    if ( $data === NULL )
      $data = padRequestIs ( 'POST' ) ? $_POST : $_GET;

    $errors = [];

    foreach ( $rules as $field => $list ) {

      $list   = is_array ( $list ) ? $list : explode ( '|', (string) $list );
      $list   = array_values ( array_filter ( array_map ( 'trim', $list ), 'strlen' ) );
      $value  = padFormFind ( $data, $field ) [1] ?? '';
      $value  = is_string ( $value ) ? trim ( $value ) : $value;

      // A field that is an object read like an array - an ArrayObject - is a list, one that
      // is text a Stringable: any other was cast to a text, and the request ended on it.

      if ( is_object ( $value ) and ! $value instanceof Stringable )
        $value = ( $value instanceof Traversable ) ? iterator_to_array ( $value ) : get_object_vars ( $value );

      $number = (bool) array_intersect ( array_map ( 'strtolower', $list ), [ 'numeric', 'integer' ] );
      $empty  = ( $value === '' or $value === NULL or $value === [] );

      // A list belongs to a field named for one - tags[] - and a field of one value posted
      // as a list breaks every rule it has: name[]=x passed required and color[]=red passed
      // in:, and the page went on with a list where its form has one text.

      $single = ( is_array ( $value ) and ! str_ends_with ( (string) $field, '[]' ) );

      foreach ( $list as $rule )
        if ( ! in_array ( strtolower ( explode ( ':', $rule, 2 ) [0] ), padValidateRules, TRUE ) )
          padValidateUnknown ( $rule );

      foreach ( $list as $rule ) {

        $parts = explode ( ':', $rule, 2 );
        $name  = strtolower ( $parts [0] );
        $arg   = $parts [1] ?? '';

        if ( $empty and $name != 'required' and $name != 'accepted' )
          continue;

        if ( ! $single and padValidateRule ( $name, $arg, $value, $data, $number ) )
          continue;

        $padFormErrorParts [$field] = padValidateMessage ( $field, $name, $arg, $number, $messages );

        $errors [$field] = padValidateText ( $padFormErrorParts [$field], padValidateName ( $field ) );

        break;

      }

    }

    $padFormErrors = array_replace ( $padFormErrors, $errors );

    return $errors;

  }

  // A list - the value of a field named name[], a multiple choice - asks required for an
  // item, and in: holds every item to its list. Any other rule speaks about one
  // value, which a list is not: color[]=evil, posted where the form has one color field,
  // passed in:, email, integer and every other rule, and the page went on with a list its
  // rules never looked at.

  function padValidateRule ( $name, $arg, $value, $data, $number ) {

    if ( is_array ( $value ) )
      return match ( $name ) {
        'required' => count ( $value ) > 0,
        'in'       => count ( $value ) == count ( array_filter ( $value, fn ( $one ) =>
                        is_string ( $one ) and padValidateRule ( 'in', $arg, trim ( $one ), $data, $number ) ) ),
        default    => FALSE
      };

    $value = (string) $value;
    $size  = $number ? (float) $value : mb_strlen ( $value );
    $other = ( $name == 'same' ) ? ( padFormFind ( $data, $arg ) [1] ?? '' ) : '';

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
      'same'     => is_scalar ( $other ) and $value === trim ( (string) $other ),
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
  // name and the engine's own switches: a search box keeps what was asked. The page's name
  // is the query string's first name only when the query string names the page: on a clean
  // URL, /shop/search?q=pad, the path does, and the first name is the field
  // (inits/page.php, lib/route.php).

  function padFormSource () {

    global $padFormStack, $padRoutePath;

    $form = $padFormStack ? end ( $padFormStack ) : NULL;

    if ( $form and $form ['method'] == 'get' ) {
      $keys = array_keys ( $_GET );
      if ( ( $padRoutePath ?? '' ) === '' or padRouteQuery () )
        $keys = array_slice ( $keys, 1 );
      $asked = array_filter ( $keys, fn ( $key ) => ! padEngineName ( (string) $key ) );
      return $asked ? $_GET : NULL;
    }

    return padFormCameBack ( $form ['name'] ?? '' ) ? $_POST : NULL;

  }

  function padFormValue ( $name, $default = '' ) {

    $source = padFormSource ();

    [ $found, $value ] = ( $source === NULL ) ? [ FALSE, NULL ] : padFormFind ( $source, $name );

    return ( $found and is_string ( $value ) ) ? $value : $default;

  }

  // A field's value in the posted data, found where PHP files it: user[email] is
  // $_POST ['user'] ['email'], tags[] the list $_POST ['tags'], and first.name arrives as
  // first_name. The rules and the refill looked for $_POST ['user[email]'], found nothing,
  // and every rule but required passed what was posted. A key that is the name itself -
  // data handed to padValidate can have one - is found first, and data handed to it can
  // be an object read like an array, an ArrayObject. [ found, value ].

  function padFormFind ( $data, $name ) {

    $name = (string) $name;

    if ( padFormHas ( $data, $name ) )
      return [ TRUE, $data [$name] ];

    foreach ( padFormPath ( $name ) as $level => $segment ) {

      if ( $segment === '' and $level )
        break;

      if ( ! padFormHas ( $data, $segment ) )
        return [ FALSE, NULL ];

      $data = $data [$segment];

    }

    return [ TRUE, $data ];

  }

  function padFormHas ( $data, $key ) {

    if ( is_array ( $data ) )
      return array_key_exists ( $key, $data );

    return ( $data instanceof ArrayAccess and $data->offsetExists ( $key ) );

  }

  // A field name read as PHP reads it (main/php_variables.c): the spaces before it dropped,
  // a space or a dot before the first [ made an underscore, then each [key] a level, [] the
  // list itself - up to a ] that no [ follows. A [ that never closes is an underscore too.

  function padFormPath ( $name ) {

    $name = ltrim ( $name, ' ' );

    if ( ! preg_match ( '/^([^\[]*)((?:\[[^\]]*\])+)/', $name, $match ) )
      return [ strtr ( $name, ' .[', '___' ) ];

    preg_match_all ( '/\[([^\]]*)\]/', $match [2], $keys );

    return array_merge ( [ strtr ( $match [1], ' .', '__' ) ], $keys [1] );

  }

  // The error shows where its form came back - beside the fields of the form that posted,
  // and not beside a field of the same name in another form of the page. A file field's
  // error is padUpload's, when it refused the file.

  function padFormError ( $name, $label = '' ) {

    global $padFormErrors, $padFormErrorParts, $padUploadErrors, $padUploadErrorParts;

    $text  = $padFormErrors     [$name] ?? $padUploadErrors     [$name] ?? NULL;
    $parts = $padFormErrorParts [$name] ?? $padUploadErrorParts [$name] ?? NULL;

    if ( $text === NULL or padFormSource () === NULL )
      return '';

    if ( $label !== '' and $parts )
      return padValidateText ( $parts, padValidateLabel ( $label ) );

    return (string) $text;

  }

  // A field refills from the request, so its value may carry a stand-in of a quote or the
  // backslash - U+E022, U+E027, U+E05C - that exits.php turns into the live character after
  // this escaping: it is escaped as the character it stands for (padUnprotectQuotes), as the
  // pipe escapers are, or value="{...}" let the request close the attribute and add a
  // handler.

  function padFormEscape ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES, 'UTF-8' );

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

      $bare = padAttrsBare ( $expr );

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

    [ $name, $take, $attrs ] = padFormItems ( 'input', [ 'type', 'label', 'value', 'id', 'checked', 'rules' ] );

    padFormRulesCheck ( 'input', $name, $take );

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

    [ $name, $take, $attrs ] = padFormItems ( 'textarea', [ 'label', 'value', 'id', 'rules' ] );

    padFormRulesCheck ( 'textarea', $name, $take );

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
  //
  // error='Please correct the errors below.' is the message above the fields when the form
  // came back with errors - those of its template's rules or of padValidate; error alone
  // says just that.

  const padFormErrorText = 'Please correct the errors below.';

  function padFormOpen () {

    global $padFormStack, $padFormErrors, $padUploadErrors;

    [ $name, $take, $attrs ] = padFormItems ( 'form', [ 'method', 'error' ] );

    $method = strtolower ( (string) ( $take ['method'] ?? 'post' ) );
    $open   = '<form method="' . padFormEscape ( $method ) . '"' . ( $attrs ? ' ' . implode ( ' ', $attrs ) : '' ) . '>';

    // The token only goes along to this site, as padCsrfForms decides (lib/csrf.php): a
    // {form} with an action= on another site handed the session's token to that site, which
    // could then post as the visitor here.

    if ( $method == 'post' ) {
      if ( padCsrfFormPosts ( $open ) )
        $open .= padCsrfField ();
      if ( $name !== '' )
        $open .= '<input type="hidden" name="' . padFormName . '" value="' . padFormEscape ( $name ) . '">';
    }

    $padFormStack [] = [ 'name' => $name, 'method' => $method, 'open' => $open ];

    $error = $take ['error'] ?? NULL;
    $error = ( $error === TRUE ) ? padFormErrorText : $error;

    if ( is_scalar ( $error ) and $error !== FALSE and (string) $error !== ''
         and ( $padFormErrors or $padUploadErrors ) and padFormSource () !== NULL )
      $padFormStack [ array_key_last ( $padFormStack ) ] ['open'] .= '<div class="error" role="alert">' . padFormEscape ( $error ) . '</div>';

  }

  // A posting form that holds a file field and names no enctype of its own gets the
  // multipart one - without it the browser sends the file's name and not the file.

  function padFormClose ( $content ) {

    global $padFormStack;

    $form = array_pop ( $padFormStack );
    $open = $form ['open'] ?? '<form>';

    if ( ( $form ['method'] ?? '' ) == 'post' and ! preg_match ( '/^<form[^>]*\senctype=/i', $open )
         and str_contains ( padUnprotect ( $content ), 'type="file"' ) )
      $open = preg_replace ( '/^<form method="post"/', '<form method="post" enctype="multipart/form-data"', $open );

    // A button in it with a formaction= on another site sends the form there, and the token
    // went along (lib/csrf.php).

    if ( ! padCsrfControlsHere ( padUnprotect ( $content ) ) )
      $open = str_replace ( padCsrfField (), '', $open );

    return padProtect ( $open ) . $content . padProtect ( '</form>' );

  }

  // The rules a template text gives the fields of its named forms: [ form => [ field =>
  // rules ] ], a form without rules [ form => [] ]. They are read before the page's PHP has made any variable, so only what is
  // written out counts - a quoted field name and quoted rules - each evaluated as the tag
  // will evaluate it. Comments, ~ and {ignore} are taken as the engine takes them: a field
  // commented out has no rules. A form name met twice - the form in both branches of an
  // {if} - is one form with the fields of both; one field cannot have two sets of rules.
  // What would leave rules unchecked is an error, under strict mode and without it: rules
  // outside a named form, in a form that posts to another page (action=) or by GET, on a
  // file field, a rule that does not exist, rules made while the page renders.

  function padFormRulesOf ( $text ) {

    $rules = [];

    if ( ! str_contains ( (string) $text, 'rules' ) )
      return $rules;

    $masks  = [];
    $text   = padLayoutMask ( padTildeStrip ( padCommentStrip ( (string) $text ) ), $masks );
    $forms  = [];
    $offset = 0;

    preg_match_all ( '/\{(\/?)(?:pad:)?(form|input|textarea)(?=[ }\/\n\t\r])/', $text, $found, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );

    foreach ( $found as $one ) {

      $pos  = $one [0] [1];
      $open = $one [0] [0];
      $tag  = $one [1] [0] . $one [2] [0];

      if ( $pos < $offset or $tag == '/input' or $tag == '/textarea' )
        continue;

      $end = padPairTagEnd ( $text, $pos + strlen ( $open ) );

      if ( $end === FALSE )
        break;

      $offset = $end + 1;
      $parms  = substr ( $text, $pos + strlen ( $open ), $end - $pos - strlen ( $open ) );

      if ( $tag == '/form' ) {
        array_pop ( $forms );
        continue;
      }

      $items = padFormRulesItems ( $parms );

      // Every form with its name written out is there, its fields with rules or not: a post
      // of a form the text does not have kept none of its rules (padPosted).

      if ( $tag == 'form' ) {
        if ( ! str_ends_with ( rtrim ( $parms ), '/' ) ) {
          $forms [] = $items;
          $named    = padFormRulesLiteral ( $items ['name'] );
          if ( $named !== NULL and $named !== '' )
            $rules [$named] ??= [];
        }
        continue;
      }

      if ( array_key_exists ( 'rules', $items ['take'] ) )
        padFormRulesField ( $rules, $tag, $items, $forms ? end ( $forms ) : NULL );

    }

    return $rules;

  }

  // The items of a tag as they stand in the text, unevaluated: the name - the first item
  // without a name that is not a bare word, as padFormItems takes it - and the named ones.

  function padFormRulesItems ( $parms ) {

    $parms = trim ( $parms );

    if ( str_ends_with ( $parms, '/' ) )
      $parms = substr ( $parms, 0, -1 );

    $parms = trim ( padPipeSplit ( $parms ) [0] );

    if ( str_ends_with ( $parms, '/' ) )
      $parms = substr ( $parms, 0, -1 );

    $name = NULL;
    $take = [];

    foreach ( padParseOptions ( $parms ) as $item ) {

      [ $key, $expr ] = padAttrsSplit ( trim ( $item ) );

      if ( $key !== '' )
        $take [ strtolower ( $key ) ] = $expr;
      elseif ( padAttrsBare ( $expr ) )
        $take [ strtolower ( $expr ) ] = 'TRUE';
      elseif ( $expr !== '' and $name === NULL )
        $name = $expr;

    }

    return [ 'name' => $name, 'take' => $take ];

  }

  // A value written out - a quoted string or a number, without a tag in it - as the tag
  // will evaluate it; NULL for anything else.

  function padFormRulesLiteral ( $expr ) {

    if ( $expr === NULL )
      return NULL;

    $literal = padMetaLiteral ( $expr );

    if ( ! is_string ( $literal ) and ! is_int ( $literal ) and ! is_float ( $literal ) )
      return NULL;

    if ( str_contains ( (string) $literal, '{' ) )
      return NULL;

    return padFormRulesText ( padEval ( $expr ) );

  }

  function padFormRulesText ( $value ) {

    if ( is_array ( $value ) )
      $value = implode ( '|', $value );

    return padUnescape ( padUnprotect ( (string) $value ) );

  }

  function padFormRulesField ( &$rules, $tag, $items, $form ) {

    $name  = padFormRulesLiteral ( $items ['name'] );
    $list  = padFormRulesLiteral ( $items ['take'] ['rules'] );
    $field = '{' . $tag . ( $name !== NULL ? " '$name'" : '' ) . '}';

    if ( $name === NULL or $name === '' )
      return padError ( "the rules of $field need its field name written out - {" . $tag . " 'email', rules='required|email'}" );

    if ( $list === NULL )
      return padError ( "the rules of $field must be written out - rules='required|email' - they are read before the page's PHP runs" );

    $named = $form ? padFormRulesLiteral ( $form ['name'] ) : NULL;

    if ( $named === NULL or $named === '' )
      return padError ( "the rules of $field need a {form} with its name written out around it - {form 'contact'}" );

    $where = "$field in {form '$named'}";

    if ( isset ( $form ['take'] ['action'] ) )
      return padError ( "the rules of $where are never checked - the form posts to another page (action=)" );

    if ( strtolower ( (string) padFormRulesLiteral ( $form ['take'] ['method'] ?? "'post'" ) ) !== 'post' )
      return padError ( "the rules of $where are never checked - only a form that posts is validated" );

    if ( $tag == 'input' and strtolower ( (string) padFormRulesLiteral ( $items ['take'] ['type'] ?? "''" ) ) == 'file' )
      return padError ( "the rules of $where are never checked - a file field is checked by padUpload" );

    foreach ( explode ( '|', $list ) as $rule ) {
      $rule = strtolower ( trim ( explode ( ':', $rule, 2 ) [0] ) );
      if ( $rule !== '' and ! in_array ( $rule, padValidateRules, TRUE ) )
        return padError ( "the rules of $where have no rule named '$rule' - there are " . implode ( ', ', padValidateRules ) );
    }

    if ( isset ( $rules [$named] [$name] ) and $rules [$named] [$name] !== $list )
      return padError ( "$where has two sets of rules - '" . $rules [$named] [$name] . "' and '$list'" );

    $rules [$named] [$name] = $list;

  }

  // The rules of the request's page, read once from the text build/page.php kept.

  function padFormRules () {

    global $padFormRules, $padFormText;

    return $padFormRules ??= padFormRulesOf ( $padFormText ?? '' );

  }

  // The post pass, from build/page.php before the first _inits.php: a post of a named form
  // whose fields have rules in the page's template is validated against them. Run again on
  // a restart, for the page restarted to.

  function padFormPost () {

    global $padFormChecked;

    $padFormChecked = [];

    $form = $_POST [padFormName] ?? NULL;

    if ( ! padRequestIs ( 'POST' ) or ! is_string ( $form ) or $form === '' )
      return;

    $rules = padFormRules () [$form] ?? [];

    if ( $rules )
      $padFormChecked [$form] = padValidate ( $rules, $_POST );

  }

  // A field that renders with rules= must be one the post pass read: the same form, field
  // and rules in the page's own text. One from a snippet, a custom tag, a {page} or a layout
  // - or rules a tag made - would be shown and never checked.

  function padFormRulesCheck ( $tag, $name, $take ) {

    if ( ! array_key_exists ( 'rules', $take ) )
      return;

    $known = padFormRules ();
    $form  = padFormCurrent ();
    $field = '{' . $tag . " '$name'}";

    if ( $form === '' )
      return padError ( "the rules of $field need a {form} with a name around it - {form 'contact'}" );

    if ( ( $known [$form] [$name] ?? NULL ) !== padFormRulesText ( $take ['rules'] ) )
      padError ( "the rules of $field in {form '$form'} are never checked - rules= is read from the page's own template before its PHP runs, and this field stands in a snippet, a custom tag, a {page} or a layout, or its rules are made while the page renders" );

  }

?>
