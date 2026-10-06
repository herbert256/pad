/**
 * Tree-sitter grammar for PAD templates (https://github.com/herbert256/pad).
 *
 * A PAD template is text - usually HTML, injected by queries/injections.scm - with tags in
 * braces. What makes a tag a pair is not its name but what follows it: {orders} opens a
 * block when a {/orders} closes it further on, and is a single tag when nothing does, the
 * same as {echo $x}. The engine decides that by searching ahead (pad/level/tag.php), and
 * so does the external scanner (src/scanner.c): it emits the zero-width _block_start only
 * when the matching close exists, keeps the open names on a stack, and emits _block_end
 * before the close that matches the innermost one. That gives every pair a block node to
 * fold, select and move over, which a pattern over single tags cannot.
 *
 * Inside a tag the grammar stays flat - a name, then words, values, operators and pipes -
 * enough to highlight every part without modelling PAD's expression language. The lists of
 * built-in tags, functions, options and properties live in queries/highlights.scm and are
 * written there by editors/generate.php from pad/, like the editors' completion lists.
 */

const NAME = /[A-Za-z_][A-Za-z0-9_]*/;

// The tag a property or an at-reference points at: a name, or a level counted back, -2.
const TARGET = /[A-Za-z_][A-Za-z0-9_]*|-?[0-9]+/;

module.exports = grammar({
  name: 'pad',

  externals: $ => [
    $._block_start,
    $._block_end,
    $.raw_text,
    $._error_sentinel,
  ],

  extras: _ => [/\s+/],

  word: $ => $.identifier,

  rules: {
    template: $ => repeat($._node),

    _node: $ => choice(
      $.text,
      $.comment,
      $.construct,
      $.block,
      $.ignore_block,
      $.tag,
      $.field_tag,
      $.close_tag,
    ),

    // Text runs to the next brace or @; the whitespace in front of it is an extra, as in
    // front of every node. A brace that cannot open a tag - {"a": 1} in a {data} block, {
    // in a script - is text with the character after it.
    text: _ => token(choice(
      /[^{@\s][^{@]*/,
      /\{[^A-Za-z_$%!?#&^\/~\-]/,
      /\{-[^-]/,
      /\{--[^\s]/,
      '@',
    )),

    // {# ... #} and {-- ... --}: both close at the first #} / --} after them, and {--
    // must be followed by whitespace. The option sigil {#name} or {#name | pipe} is no
    // comment - the engine's padCommentStrip leaves it - so a {# that opens a name opens a
    // comment only when what follows the name (and its spaces) is not } or |; without that,
    // <h2>{#title}</h2> ... {# note #} was one comment from the sigil to the note's end.
    comment: _ => token(choice(
      /\{#([^A-Za-z_#]|#+[^#}])([^#]|#+[^#}])*#+\}/,
      /\{##+\}/,
      /\{#[A-Za-z_][A-Za-z0-9_]*\s*#+([^#}]([^#]|#+[^#}])*#+)?\}/,
      /\{#[A-Za-z_][A-Za-z0-9_]*[^A-Za-z0-9_\s}|#]([^#]|#+[^#}])*#+\}/,
      /\{#[A-Za-z_][A-Za-z0-9_]*\s+[^\s}|#]([^#]|#+[^#}])*#+\}/,
      /\{--\s([^-]|-[^-]|--+[^-}])*--+\}/,
    )),

    construct: _ => /@(page|content|start|end|else|tidy)@/,

    block: $ => seq(
      $._block_start,
      $.open_tag,
      optional($.body),
      $._block_end,
      $.close_tag,
    ),

    body: $ => repeat1($._node),

    // {ignore} ... {/ignore}: the content is not PAD - JavaScript, CSS, JSON - and is kept
    // as one raw_text node, which the injections hand to HTML.
    ignore_block: $ => seq(
      $._block_start,
      alias($._ignore_open_tag, $.open_tag),
      optional($.raw_text),
      $._block_end,
      $.close_tag,
    ),

    _ignore_open_tag: $ => seq(
      $._open,
      alias($._ignore_name, $.tag_name),
      optional($.arguments),
      $._close,
    ),

    _ignore_name: $ => field('name', alias('ignore', $.identifier)),

    tag: $ => seq($._open, $.tag_name, optional($.arguments), $._close),

    open_tag: $ => seq($._open, $.tag_name, optional($.arguments), $._close),

    close_tag: $ => seq($._open, '/', $.tag_name, optional($.arguments), $._close),

    // {$name}, {!raw}, {?query}, {#parm}, {&property}, {^json}
    field_tag: $ => seq($._open, $.variable, optional($.arguments), $._close),

    // A ~ just inside a brace takes the whitespace on that side.
    _open: _ => seq('{', optional(token.immediate('~'))),
    _close: _ => seq(optional('~'), '}'),

    // if, app:mytag, first@items - a prefix and a property are written against the name.
    tag_name: $ => choice(
      field('name', $.identifier),
      seq(
        field('prefix', alias($.identifier, $.prefix)),
        token.immediate(':'),
        field('name', alias(token.immediate(NAME), $.identifier)),
      ),
      seq(
        field('property', alias($.identifier, $.property)),
        token.immediate('@'),
        optional(field('target', alias(token.immediate(TARGET), $.identifier))),
      ),
    ),

    // Everything after the name, as written: parameters, options, an expression, pipes.
    arguments: $ => repeat1($._argument),

    _argument: $ => choice(
      $.string,
      $.number,
      $.format,
      $.variable,
      $.identifier,
      $.option,
      $.prefixed_name,
      $.property_reference,
      $.pipe,
      $.operator,
      $.placeholder,
      $.field_tag,
      $.tag,
      ',', '=', ':', '(', ')', '[', ']',
    ),

    // sort='name', callback='double', rows=5
    option: $ => prec.right(1, seq(
      field('name', alias($.identifier, $.option_name)),
      '=',
      optional(field('value', choice($.string, $.number, $.variable, $.identifier, $.field_tag))),
    )),

    // | upper, | date('Y-m-d'), | function:money, | + 1
    pipe: $ => prec.right(seq(
      '|',
      optional(seq(
        optional(seq(field('prefix', alias($.identifier, $.prefix)), token.immediate(':'))),
        field('function', alias($.identifier, $.function_name)),
      )),
    )),

    // sequence:fibonacci(8), php:strlen(@)
    prefixed_name: $ => seq(
      field('prefix', alias($.identifier, $.prefix)),
      token.immediate(':'),
      field('name', alias(token.immediate(NAME), $.identifier)),
    ),

    // first@items, count@orders
    property_reference: $ => seq(
      field('property', alias($.identifier, $.property)),
      token.immediate('@'),
      field('target', alias(token.immediate(TARGET), $.identifier)),
    ),

    // the current value in an expression: {echo 50 | @ * 4}
    placeholder: _ => '@',

    // $name, $user.name, $1 (a parameter), $-2 (a level back), &cool:parameter:1,
    // $first@orders, $<3@myRange, $*@myRange, and $$ (the current value)
    variable: _ => token(choice(
      seq(
        /[$%!?#&^]+/,
        choice(/[A-Za-z_][A-Za-z0-9_]*/, /-?[0-9]+/, /[<>*][0-9]*/),
        repeat(/[.:]([A-Za-z_][A-Za-z0-9_]*|[0-9]+)/),
        optional(/@([A-Za-z_][A-Za-z0-9_]*|-?[0-9]+)/),
      ),
      '$$',
    )),

    string: _ => token(choice(
      /'([^'\\]|\\.)*'/,
      /"([^"\\]|\\.)*"/,
    )),

    number: _ => /\d+(\.\d+)?/,

    // a printf format as a pipe: {$nbr | %.2f}
    format: _ => token(prec(1, /%[-+ 0#]*('.)?[0-9]*(\.[0-9]+)?[bcdeEfFgGosuxX]/)),

    operator: _ => token(choice('==', '!=', '<=', '>=', '<>', '&&', '..', /[-+*\/<>!.%^?&]/)),

    identifier: _ => NAME,
  },
});
