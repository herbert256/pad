# Regression: the 'file' output type

## Introduction

Regression test for `$padOutputType = 'file'`. The payload page renders a marker through a
pipe, and the index fetches it and asserts how this output type is supposed to deliver a
page: the body lands on disk, and the visitor is handed the `done` page in its place. The
crawl compares the index, so a writer that stops behaving turns the page from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the payload, checks the disk, and states the verdict |
| `payload.php/pad` | A page with a recognisable body |
| `done.pad` | Where the file writer sends the visitor after the write |
| `_config/config.php` | Chooses the 'file' output type for the payload, `_common` off |
