# Modern WPCS Standard Project Rules for AI Agents

## Project Overview

**Modern WPCS Standard** (`contactjavas/modern-wpcs-standard`) is a [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) standard: a set of custom sniffs for modern (PHP >= 8.4) WordPress development. It is not meant for WordPress Core work.

### Purpose
- Provide the `ModernWpcsStandard` sniffs (strict types, no `extract()`, no global functions, no assignment alignment, etc.)
- Act as the custom-sniff layer of the meta-ruleset [`modern-wpcs-ruleset`](https://github.com/contactjavas/modern-wpcs-ruleset), which combines it with WordPress-Core, VariableAnalysis and ImportDetection

### Tech Stack
- **Language**: PHP `^8.4` (CI matrix: 8.4, 8.5)
- **Framework**: PHP_CodeSniffer `^4.0.1` → [Wiki](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki)
- **Tests**: PHPUnit `^13`
- **Package Manager**: Composer (package type `phpcodesniffer-standard`, registered via `dealerdirect/phpcodesniffer-composer-installer`)
- **Dev machine**: Windows + PowerShell. `php` and `composer` are on `PATH`.

### Origin
- Forked from [Automattic/phpcs-neutron-standard](https://github.com/Automattic/phpcs-neutron-standard) (`upstream` remote). Upstream is not tracked actively; do not pull from it unless asked.

### Constraints
- Must use the existing codebase and architecture
- Must comply with existing coding standards
- `GUIDELINES.md` is the human-readable spec of what the sniffs enforce. Keep it consistent with sniff behaviour.

---

## Package Management

- **Package Manager**: Always use `composer`
- **Commands**:
  - Install: `composer install`
  - Add dev package: `composer require --dev <package>`
  - Update: `composer update`
  - Validate (CI runs this): `composer validate --strict`
- `composer.lock` is gitignored; do not commit it.

---

## Security

- Sniffs run on users' code. They must never execute, include or `eval` the code they analyse; work only on the token stream.
- Never hardcode sensitive data (tokens, credentials) in code, CI or fixtures
- Handle unexpected token streams defensively (`findNext`/`findPrevious` may return `false`; parse errors leave missing `scope_*`/`parenthesis_*` keys)
- Keep dependency constraints current when security advisories appear (`composer audit`)

---

## Code Quality Standards

### 1. Code Structure
- Every PHP file starts with `declare( strict_types=1 );`
- Use **tabs** for indentation (enforced by `phpcs.xml.dist`: PSR2 + tab indent + K&R braces)
- Type every parameter and return value; sniff `process()` signatures are `process(File $phpcsFile, int $stackPtr): void`
- Maintain consistency with existing code patterns

### 2. Sniffs
- Location and naming: `ModernWpcsStandard/Sniffs/<Category>/<Name>Sniff.php`, namespace `ModernWpcsStandard\Sniffs\<Category>`. The resulting sniff code is `ModernWpcsStandard.<Category>.<Name>.<ErrorCode>`.
- **Sniff codes are public API.** Users and `modern-wpcs-ruleset` reference them in `phpcs.xml`. Do not rename a category, sniff or error code (the third argument of `addError`/`addWarning`) without being asked, and call it out as a breaking change.
- Prefer PHPCS utilities over hand-rolled token scanning: `File::getMethodProperties()`, `File::getMethodParameters()`, `File::getDeclarationName()`, `Tokens::EMPTY_TOKENS`, token keys `scope_opener` / `scope_closer` / `parenthesis_opener` / `parenthesis_closer`, and `$phpcsFile->eolChar` (never hard-code `"\n"`; CRLF files must work).
- Matching function names: PHP function names are case-insensitive and may be fully qualified (`\extract()`). Use `SniffHelpers::getGlobalFunctionCallName()` and `SniffHelpers::isFunctionCall()` so method calls, static calls, `new`, declarations and namespaced functions are not flagged.
- Fixers must converge. A fix must never remove code or meaningful blank lines, and must not fight another sniff in `modern-wpcs-ruleset`.
- `ModernWpcsStandard/SniffHelpers.php` is shared by several sniffs. Changing what a helper returns affects all of them; run the full test suite.

### 3. Performance
- Sniffs run on every token of every file. Register only the tokens you need, and return early.
- Avoid scanning the whole file from inside `process()`. Bound searches with `scope_closer` / `parenthesis_closer`.
- Don't micro-optimise without a measurement. The whole standard takes about 66 ms on a 3,000-line file.

---

## Development Workflow

### 1. Implementation

- Carefully read and interpret all user instructions
- Write a failing fixture or test first when fixing a false positive or false negative
- Follow existing code patterns in the project
- Ask for clarification when requirements are ambiguous or missing

### 2. Tests

- Tests live in `tests/Sniffs/<Category>/<Name>SniffTest.php` with fixture files `*Fixture.php` next to them (fixers: `Fixed*Fixture.php` holds the expected output).
- Pattern (see `tests/SniffTestHelper.php`):
  ```php
  $helper = new SniffTestHelper();
  $phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
  $phpcsFile->process();
  $this->assertEquals([11, 21], $helper->getErrorLineNumbersFromFile($phpcsFile));
  // fixers:
  $this->assertEquals(file_get_contents($fixedFixture), $helper->getFixedFileContents($phpcsFile));
  ```
- Helpers return **line numbers only**, so several messages on one line collapse into one entry. To assert counts, inspect `$phpcsFile->getErrors()[$line]` directly.
- Fixtures are excluded from lint and from release archives (`tests/` is `export-ignore`). Save them with **LF** line endings unless the test is about CRLF. PowerShell `Set-Content` can produce mixed endings; use the Write tool.

### 3. Verification

**Always verify** before reporting a task as done. Both commands must pass:

```
composer test     # PHPUnit, --testdox
composer lint     # phpcs on ModernWpcsStandard/ using phpcs.xml.dist
```

Quick manual check of a single sniff on an ad-hoc file (keep scratch files outside the repo):

```
php vendor/bin/phpcs -s --standard=ModernWpcsStandard/ruleset.xml --sniffs=ModernWpcsStandard.<Category>.<Name> path/to/file.php
php vendor/bin/phpcbf --standard=ModernWpcsStandard/ruleset.xml --sniffs=ModernWpcsStandard.<Category>.<Name> path/to/file.php
```

### 4. Testing Loop

```
1. Add/adjust fixture that reproduces the issue
2. Implement changes
3. Run composer test && composer lint
4. If incorrect → fix → test again → examine again
5. Repeat until correct
```

---

## Error Handling Protocol

### 1. Error Analysis
- Parse and analyze error messages. Use `phpcs -s` so the sniff code is shown.
- Identify scope and isolate the root cause. Dump the token stream (`--report=…`, or `$phpcsFile->getTokens()`) when token positions are unclear.

### 2. Solution Planning
- Consider multiple solutions
- Select the optimal fix based on risk and scope (sniff-local fix before shared-helper change)

### 3. Fix & Validate
- Implement the fix with minimal disruption
- Add a fixture case for the bug, then run the full suite to check for regressions

### 4. Documentation
- Update `GUIDELINES.md` if what the standard enforces changed
- Report the issue, the fix, and any follow-up the downstream ruleset needs

---

## Release & Downstream Impact

- `modern-wpcs-ruleset` (sibling checkout at `../modern-wpcs-ruleset`) requires this package by tag (`contactjavas/modern-wpcs-standard ^2.0.0`). Fixes reach it only after a tagged release.
- The PHP requirement is now `^8.4` (latest tag `v2.0.0` predates that), so the next release should be **3.0.0**. The ruleset must then bump its constraint and its own PHP requirement.
- Renaming or removing a sniff or error code, or changing a fixer, can break the ruleset's `ruleset.xml` overrides or its CI fixtures. Check `../modern-wpcs-ruleset/ModernWpcsRuleset/ruleset.xml` when touching such things.
- Do not create tags or releases unless the user asks.

---

## Git Workflow

> **IMPORTANT**: Do NOT commit or push unless the user explicitly asks you to.

### When Asked to Commit & Push

1. **Stage changes**: `git add .`. Check `git status` first, so nothing unexpected is included.

2. **Commit with this repo's format**:
   - **Subject line**: imperative mood, naming the sniff or area, e.g. `Fix DisallowLongformArray: detect any-case array()` or `Housekeeping: …`
   - **Description**: Structured explanation (can be a list). Do not mention the AI agent, and do not add an AI co-author.

   ```bash
   git commit -m "Fix <Sniff>: brief subject" -m "Detailed description here.

   - Change 1
   - Change 2"
   ```

3. **Push to GitHub**: `git push` (remote `origin`, branch `master`). Never push to `upstream`.

---

## Project Structure

```
modern-wpcs-standard/
├── .agents/
│   └── tasks/                  # Local task/audit notes (gitignored, may not exist)
├── .github/workflows/ci.yml    # composer validate, composer test, composer lint on PHP 8.4/8.5
├── ModernWpcsStandard/
│   ├── ruleset.xml             # Standard definition (auto-includes all sniffs)
│   ├── SniffHelpers.php        # Shared static helpers
│   └── Sniffs/
│       ├── Arrays/             # DisallowLongformArray
│       ├── AssignAlign/        # DisallowAssignAlign
│       ├── Conditions/         # DisallowConditionAssignWithoutConditional
│       ├── Constants/          # DisallowDefine
│       ├── Extract/            # DisallowExtract
│       ├── Functions/          # DisallowCallUserFunc, LongFunction, TypeHint, VariableFunctions
│       ├── Globals/            # DisallowGlobalFunctions
│       ├── MagicMethods/       # DisallowMagicGet/Set, DisallowSerializableInterface, RiskyMagicMethod
│       ├── StrictTypes/        # RequireStrictTypes
│       └── Whitespace/         # DisallowMultipleNewlines, RequireNewlineBetweenFunctions, RequireParenthesisSpacing
├── tests/
│   ├── bootstrap.php           # Loads PHPCS test bootstrap + SniffTestHelper
│   ├── SniffTestHelper.php
│   └── Sniffs/<Category>/      # *SniffTest.php + *Fixture.php
├── composer.json
├── GUIDELINES.md               # Coding guidelines the standard enforces
├── phpcs.xml.dist              # Lint config for this repo's own code
├── phpunit.xml.dist
└── README.md
```

---

## Important

- Maintain clarity and request confirmation when needed
- Ensure high-quality, scoped, and standards-compliant implementations
- Regularly report progress during multi-step tasks
- Pause and confirm at critical decision points (sniff-code renames, behaviour changes, version bumps)
- Clearly report errors or blockers, and propose practical resolutions

---

## Reference Documentation

**Do NOT read these preemptively.** Consult one only when the task touches its domain:

| Document | Path | Description |
| --- | --- | --- |
| **Coding Guidelines** | [`GUIDELINES.md`](GUIDELINES.md) | The rationale behind each rule (strict types, globals, magic methods, newlines, …). |
| **Usage / Installation** | [`README.md`](README.md) | How consumers install and configure the standard. |
| **Downstream ruleset** | `../modern-wpcs-ruleset/AGENTS.md` | Rules for the meta-ruleset that consumes this standard. |
| **Audit tasks (local)** | `.agents/tasks/<audit>/README.md` | Gitignored audit/task notes with their own agent workflow. Only use them when the user points you there. |
