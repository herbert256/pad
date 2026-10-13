---
name: pad-record-answer
description: Re-record the stored answer of a PAD regression test in a covering suite (regression, sequence, manual, other) after a deliberate change - only once every difference between the stored and the new answer is explained. Use when a page of an application changed on purpose and its suite now fails, e.g. the reference index after a new tag name.
---

# Re-recording a stored answer

A covering suite (`regression`, `sequence`, `manual`, `other`) keeps what each page of an
application answered, in `apps/regression/<suite>/<name>.txt`. When a change alters a page on
purpose, that test fails until its answer is recorded again. Recording is how a wrong answer
gets locked in, so it is never done blind.

The handwritten suites - `pages`, `common`, `errors`, `framework` - never record: their
answers are intent and are edited by hand. A stored `/pattern/` or `HTTP <code>` answer is
handwritten wherever it lives and refuses too.

## 1. See every difference

- Run the suite so its current result holds the test as FAILED: `pad_test suite=<suite>` (check
  `pgrep -fl 'ci\.sh'` first - not while a gate runs).
- `pad_case app=<application> page=<page>` shows where the stored answer and what the page
  answers now part. For a long body, diff the store file against the page's answer.

## 2. Explain each one

Every difference needs a reason in the change you made, said in one line each, e.g.:

- a new tag, function or option name in `other/reference/index.txt` - only the new name added
- an intended change of output - the fix shows on this page as it should
- tidy, a harvested link, line numbers in a trace page moved by a change to `pad/**/*.php`

A difference you cannot explain is not recorded: it is a bug of the change, another
session's work in the shared checkout, or a page that answers differently per request
(time, random, a session id). Find out which first.

## 3. Record

Recording is two requests to the runner. The first shows the stored answer beside the new one
and writes nothing; its link carries a hash of exactly that body. The second writes only when
a refetch still matches the hash:

```bash
u='http://localhost/pad/regression/main/?record&suite=<suite>&name=<name>'
go=$(curl -s "$u" | grep -o 'go=[0-9a-f]\{12\}' | head -1)
[ -n "$go" ] && curl -s -o /dev/null "$u&$go"
```

No `go=` link means the runner refused - its error says why (handwritten suite, a test that
is not failing in the current result, a page that answers an HTTP error).

## 4. Check and commit

- `pad_test suite=<suite>` again: the test is ok now.
- `git diff -- apps/regression/<suite>/<name>.txt` shows only the explained differences.
- Commit the answer with the change that caused it (pad-commit skill), by path. An answer
  moved by a build's output gets its own commit, its message naming the build.
