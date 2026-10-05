# Sequence Application

## Introduction

Demonstration and testing of PAD's mathematical sequence subsystem.

## Structure

```
sequence/
├── _data/           # Sequence data
├── _include/        # Sequence includes
├── _inits.pad       # Sequence wrapper
├── _inits.php       # Sequence initialization
├── _lib/            # Sequence libraries
├── basic/           # Basic sequence examples (80+ sequences)
├── check/           # Sequence validation tests
├── concepts/        # Sequence concept documentation
├── keepRemoveFlag/  # Filter flag examples
├── play/            # Sequence playground
├── random/          # Random sequence examples
├── specials/        # Special sequence types
├── types/           # Sequence type examples
├── index.pad/.php   # Sequence index
├── actions.pad/.php # Sequence actions
├── examples.pad/.php # Sequence examples
├── reference.pad    # Sequence reference
├── reference2.pad/.php # Extended reference
├── gallery.pad/.php # Every type as a sparkline beside its OEIS entry
├── listen.pad/.php  # A sequence played as notes (?listen&type=recaman)
├── guess.pad/.php   # Guess the next term
└── sequences.php    # Sequence utilities
```

## Features

- **80+ mathematical sequences**: Fibonacci, primes, triangular, etc.
- **Sequence actions**: Transform, filter, combine sequences
- **Interactive examples**: Live sequence generation
- **Type demonstrations**: Various sequence types and patterns
- **Sequences you can see and hear** (`_lib/fun.php`): the Gallery draws the first 24 terms
  of every type that needs no parameter with `{sparkline sequence=$type}` and links the OEIS
  entry the terms come from - found in the table the `oeis` type reads, the answers kept in
  `DATA/sequence/oeis.json`; Listen turns the terms into notes (the term modulo 60, up from
  C2) and writes them as a WAV file into the page, so `<audio>` plays them without
  JavaScript; Guess shows the first seven terms of a well-known sequence and asks for the
  eighth

## Sequence Types

- Natural numbers, integers, primes
- Fibonacci, Lucas, Pell sequences
- Triangular, square, pentagonal numbers
- Factorials, powers, roots
- And many more mathematical sequences

## Access

Via web browser: `http://server/sequence/`
