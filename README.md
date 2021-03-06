# Lyridiff
The app is a visual tool designed to compare and highlight differences (aka _diffing_) in music lyrics.
This is similar to plain text diffing, but is specifically geared at recognizing song text components and repeating patterns.

## Setup
After cloning, launch from the public folder. Tested against PHP built-in server.

## Tests
For the moment, the test suite covers the internals of the core diffing library. 
More unit tests are needed for diff providers and renderers.
There's no functional test coverage yet.

## Personal notes
The design of the app is intended to be demonstrative. Everything is written from the ground up combining components instead of relying on a single framework. The design is simplicistic but i tried to expose a number of concepts.

## Sources
The diff algorithm is inspired by GNU's `diff` and the renderer used in the webapp implements GNU's `wdiff` basic idea.
The algorithm is called after its creator, Douglas McIlroy.
This implementation for PHP is partially ported from Tim Peters' implementation as found in the original `difflib` Python library.

## Current output
The app uses the SideBySide renderer to output a diff-by-line, diff-by-word response. I tried to follow `diffchecker.com/diff` styling. More renderers are available, namely two Text renderers intended for console output. One works on lines (very much like the original `diff`) and the other on words (very much like the original `wdiff`).

## Todo
Tests, tests, tests.
