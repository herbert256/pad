# PAD Sample Application

This repository demonstrates **PAD** (PHP Application Driver). PAD is an inversion of control driver that separates PHP logic from HTML markup. It first executes PHP code and then merges it with the associated PAD markup to generate pages.

The `index.pad` page explains the concept:

```
  PAD is an inversion of control PHP application driver.
  - first executes the application PHP code,
  - then reads the application HTML markup code,
  - merges both and send the result to the browser.
  There is no PAD code in the application PHP code at all !
```

## Repository layout

- **hello/** – minimal "Hello World" example (`hello.php` and `hello.pad`).
- **manual/** – PAD manual pages in `.pad` format covering topics such as parsing, variables/options/parameters and more.
- **start/** – introductory examples.
- **sequence/** – examples about sequences and playbooks.
- **_tags/** – custom PAD tags used by the examples and manual.
- **_regression/** – stored HTML/text output for regression testing.

PAD pages use constructs such as `{example}` or `{tag}` as seen in the manual pages. For instance, `manual/parse.pad` describes on‑the‑fly parsing of PAD tags:

```
PAD parses on the fly (on demand) it searches for the first {close}, then it takes the first {open} before it, and analyses it to see if it is a PAD tag or a PAD variable.
```

## Running

A PHP interpreter with the PAD engine is required. When PHP is available, you can serve the project locally:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000/index.php` in a browser to explore the examples and manual.

## Purpose

The repository provides a playground for learning PAD concepts and includes a collection of examples and documentation. It can be used as a reference implementation for how PHP scripts and PAD markup interact.
