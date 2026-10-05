// External scanner for the PAD grammar: decides which tags are pairs, the way the engine
// does in pad/level/tag.php.
//
// {name ...} opens a block when a {/name} closes it further on - the first one with as many
// {name} openers as {/name} closers in between - and is a single tag otherwise; {name .../}
// is always single. So at the start of a tag the scanner reads the name and searches ahead.
// When the close is there it emits _block_start, zero characters wide so the grammar still
// parses the tag itself, and pushes the name. Before a {/name} that matches the innermost
// open name it emits _block_end and pops. Comments are skipped in the search, as the engine
// strips them before it looks. raw_text is the content of {ignore} up to its {/ignore}.
//
// A ~ just inside a brace ({~items~}, {~/items~}) is whitespace control and does not change
// the name. The names on the stack are serialised for incremental parsing.

#include "tree_sitter/parser.h"

#include <stdbool.h>
#include <stdint.h>
#include <stdlib.h>
#include <string.h>

enum TokenType {
  BLOCK_START,
  BLOCK_END,
  RAW_TEXT,
  ERROR_SENTINEL,
};

#define MAX_NAME  64
#define MAX_DEPTH 128

typedef struct {
  uint32_t depth;
  uint8_t  length [MAX_DEPTH];
  char     names  [MAX_DEPTH] [MAX_NAME];
} Scanner;

static bool is_name_start ( int32_t c ) {
  return ( c >= 'a' && c <= 'z' ) || ( c >= 'A' && c <= 'Z' ) || c == '_';
}

static bool is_name_char ( int32_t c ) {
  return is_name_start ( c ) || ( c >= '0' && c <= '9' ) || c == ':' || c == '@';
}

static bool is_space ( int32_t c ) {
  return c == ' ' || c == '\t' || c == '\n' || c == '\r' || c == '\f' || c == '\v';
}

// What may follow a name for it to be the whole name: a space, the closing brace, or the
// ~ of whitespace control.
static bool is_terminator ( int32_t c ) {
  return is_space ( c ) || c == '}' || c == '~';
}

static void advance ( TSLexer *lexer ) {
  lexer->advance ( lexer, false );
}

// Reads a name at the lookahead into buf; returns its length, 0 when there is none or it is
// longer than a stack slot.
static unsigned read_name ( TSLexer *lexer, char *buf ) {
  unsigned n = 0;
  while ( is_name_char ( lexer->lookahead ) && ! lexer->eof ( lexer ) ) {
    if ( n >= MAX_NAME ) return 0;
    buf [n++] = (char) lexer->lookahead;
    advance ( lexer );
  }
  return n;
}

// Steps over the rest of an opener, just after its name, to its closing brace - by brace
// depth, so a parameter holding a tag of its own is read to the real end. Tells whether
// the opener closes itself: a / right before the }. FALSE when the text ends first.
static bool opener_end ( TSLexer *lexer, bool *self_closing ) {
  int depth = 1;
  int32_t previous = 0;
  while ( ! lexer->eof ( lexer ) ) {
    int32_t c = lexer->lookahead;
    if ( c == '{' ) depth++;
    if ( c == '}' && --depth == 0 ) {
      *self_closing = ( previous == '/' );
      advance ( lexer );
      return true;
    }
    previous = c;
    advance ( lexer );
  }
  return false;
}

// Skips a comment whose opening {# or {-- has been read, up to and including its end.
static void skip_comment ( TSLexer *lexer, int32_t mark ) {
  int run = 0;
  while ( ! lexer->eof ( lexer ) ) {
    int32_t c = lexer->lookahead;
    advance ( lexer );
    if ( c == '}' && run >= ( mark == '#' ? 1 : 2 ) ) return;
    run = ( c == mark ) ? run + 1 : 0;
  }
}

// Searches the rest of the text for the {/name} that closes the opener just read, counting
// the {name} openers and {/name} closers on the way.
static bool find_close ( TSLexer *lexer, const char *name, unsigned length ) {
  int open = 0;
  char buf [MAX_NAME];

  while ( ! lexer->eof ( lexer ) ) {

    if ( lexer->lookahead != '{' ) {
      advance ( lexer );
      continue;
    }

    advance ( lexer );

    if ( lexer->lookahead == '#' ) {
      advance ( lexer );
      skip_comment ( lexer, '#' );
      continue;
    }

    if ( lexer->lookahead == '-' ) {
      advance ( lexer );
      if ( lexer->lookahead != '-' ) continue;
      advance ( lexer );
      if ( ! is_space ( lexer->lookahead ) ) continue;
      skip_comment ( lexer, '-' );
      continue;
    }

    if ( lexer->lookahead == '~' ) advance ( lexer );

    bool closing = false;
    if ( lexer->lookahead == '/' ) {
      closing = true;
      advance ( lexer );
    }

    if ( ! is_name_start ( lexer->lookahead ) ) continue;

    unsigned n = read_name ( lexer, buf );
    if ( n != length || memcmp ( buf, name, n ) != 0 || ! is_terminator ( lexer->lookahead ) )
      continue;

    if ( closing ) {
      if ( open == 0 ) return true;
      open--;
      continue;
    }

    bool self_closing = false;
    if ( ! opener_end ( lexer, &self_closing ) ) return false;
    if ( ! self_closing ) open++;

  }

  return false;
}

// The content of {ignore}: everything up to the {/ignore} that ends it.
static bool scan_raw ( Scanner *s, TSLexer *lexer, const bool *valid ) {
  static const char close [] = "ignore";
  bool any = false;

  for ( ;; ) {

    if ( lexer->eof ( lexer ) ) {
      lexer->mark_end ( lexer );
      if ( ! any ) return false;
      lexer->result_symbol = RAW_TEXT;
      return true;
    }

    if ( lexer->lookahead != '{' ) {
      advance ( lexer );
      any = true;
      continue;
    }

    lexer->mark_end ( lexer );
    advance ( lexer );
    if ( lexer->lookahead == '~' ) advance ( lexer );

    bool match = false;
    if ( lexer->lookahead == '/' ) {
      advance ( lexer );
      unsigned i = 0;
      while ( close [i] && lexer->lookahead == close [i] ) { advance ( lexer ); i++; }
      match = ( close [i] == 0 && is_terminator ( lexer->lookahead ) );
    }

    if ( match ) {
      if ( any ) {
        lexer->result_symbol = RAW_TEXT;
        return true;
      }
      // Nothing between {ignore} and {/ignore}: what was read is the close itself.
      if ( valid [BLOCK_END] && s->depth > 0 && s->length [s->depth - 1] == 6
           && memcmp ( s->names [s->depth - 1], close, 6 ) == 0 ) {
        s->depth--;
        lexer->result_symbol = BLOCK_END;
        return true;
      }
      return false;
    }

    any = true;

  }
}

void *tree_sitter_pad_external_scanner_create ( void ) {
  return calloc ( 1, sizeof ( Scanner ) );
}

void tree_sitter_pad_external_scanner_destroy ( void *payload ) {
  free ( payload );
}

// depth, then per name its length and its characters - as many names as fit the buffer.
unsigned tree_sitter_pad_external_scanner_serialize ( void *payload, char *buffer ) {
  Scanner *s = (Scanner *) payload;
  unsigned size = 1, count = 0;
  for ( ; count < s->depth; count++ ) {
    unsigned need = 1 + s->length [count];
    if ( size + need > TREE_SITTER_SERIALIZATION_BUFFER_SIZE ) break;
    buffer [size++] = (char) s->length [count];
    memcpy ( buffer + size, s->names [count], s->length [count] );
    size += s->length [count];
  }
  buffer [0] = (char) count;
  return size;
}

void tree_sitter_pad_external_scanner_deserialize ( void *payload, const char *buffer, unsigned length ) {
  Scanner *s = (Scanner *) payload;
  s->depth = 0;
  if ( length == 0 ) return;
  unsigned count = (uint8_t) buffer [0], at = 1;
  for ( unsigned i = 0; i < count && at < length && i < MAX_DEPTH; i++ ) {
    unsigned n = (uint8_t) buffer [at++];
    if ( n > MAX_NAME || at + n > length ) break;
    s->length [i] = (uint8_t) n;
    memcpy ( s->names [i], buffer + at, n );
    at += n;
    s->depth = i + 1;
  }
}

bool tree_sitter_pad_external_scanner_scan ( void *payload, TSLexer *lexer, const bool *valid ) {
  Scanner *s = (Scanner *) payload;
  char name [MAX_NAME];

  // Error recovery asks for every token at once; guessing a block there only adds noise.
  if ( valid [ERROR_SENTINEL] ) return false;

  if ( valid [RAW_TEXT] ) return scan_raw ( s, lexer, valid );

  if ( ! valid [BLOCK_START] && ! valid [BLOCK_END] ) return false;

  while ( is_space ( lexer->lookahead ) ) lexer->advance ( lexer, true );

  if ( lexer->lookahead != '{' ) return false;

  lexer->mark_end ( lexer );
  advance ( lexer );
  if ( lexer->lookahead == '~' ) advance ( lexer );

  if ( lexer->lookahead == '/' ) {
    if ( ! valid [BLOCK_END] || s->depth == 0 ) return false;
    advance ( lexer );
    unsigned n = read_name ( lexer, name );
    unsigned top = s->depth - 1;
    if ( n == 0 || n != s->length [top] || memcmp ( name, s->names [top], n ) != 0
         || ! is_terminator ( lexer->lookahead ) )
      return false;
    s->depth--;
    lexer->result_symbol = BLOCK_END;
    return true;
  }

  if ( ! valid [BLOCK_START] || ! is_name_start ( lexer->lookahead ) || s->depth >= MAX_DEPTH )
    return false;

  // The tag's own name may be followed by anything that is not part of a name - an
  // opening pipe, {items|trim}, too: the engine searches for the {/items} all the same.
  unsigned n = read_name ( lexer, name );
  if ( n == 0 ) return false;

  bool self_closing = false;
  if ( ! opener_end ( lexer, &self_closing ) || self_closing ) return false;
  if ( ! find_close ( lexer, name, n ) ) return false;

  memcpy ( s->names [s->depth], name, n );
  s->length [s->depth] = (uint8_t) n;
  s->depth++;
  lexer->result_symbol = BLOCK_START;
  return true;
}
