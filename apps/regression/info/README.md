# Regression: four info modes

## Introduction

Regression test for `$padInfo` with four modes - stats, track, xml, xref - and every
option each of them honours. The probe page renders a loop, a pipe and a sequence; the
index fetches it and asserts that every mode recorded something for that very request:
the stats header on the response, the track files grown, the xml level dump written afresh
as one tree, and the probe on the cross-reference's record. The crawl compares the index,
so a mode that stops recording turns its line from yes to NO.

The trace mode is left out: it wrote some 90 files for every request of this application,
and every `./ci.sh` run left them under `DATA/trace/` - half a million files that nothing
removed. The `{trace}` tag is still tested by `regression/pages` (`misc/trace`,
`request/a_trace_then_an_error`).

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the probe and states one verdict per mode |
| `probe.php/pad` | A page with a loop, a pipe and a sequence to record |
| `_config/config.php` | The four modes by name, every option on, `_common` off |
