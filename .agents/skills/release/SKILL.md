---
name: release
description: Cut a new version of contactjavas/modern-wpcs-standard by verifying, picking the semver bump, creating an annotated vX.Y.Z tag and pushing it to origin (Composer reads versions from tags). Use when the user says "release", "tag a release", "cut a version", "publish", or "tag and push".
---

# Release (verify → pick version → tag → push)

There is no `version` field in `composer.json` and no deploy workflow. Consumers (including
`../modern-wpcs-ruleset`) install this package from the GitHub VCS repository, so **pushing a
`vX.Y.Z` tag to `origin` is the release**. A pushed tag is public and can't be taken back cleanly.

## Steps

1. **Inspect state**
   - You must be on `master`, with a clean working tree (`git status`). If there are uncommitted changes,
     stop and ask whether they belong in the release. If they do, commit them first using the format
     from `AGENTS.md` → Git Workflow.
   - `git fetch origin --tags` so the remote state is known. Local `master` must not be behind `origin/master`.
   - Latest released version: `git tag --sort=-v:refname | Select-Object -First 1`.
   - What's unreleased: `git log --oneline <latest-tag>..HEAD` and `git diff <latest-tag>..HEAD --stat`.
     If nothing is unreleased, tell the user and stop.

2. **Verify**. Every command must pass:
   ```
   composer validate --strict
   composer test
   composer lint
   ```
   Also check CI on the commit you're tagging: `gh run list --branch master --limit 1`. It must be
   `completed success` for `HEAD`. If it's running, say so and wait or ask. If it failed, stop.

3. **Pick the version** (semver). Read `git diff <latest-tag>..HEAD -- composer.json ModernWpcsStandard/`:
   - **Major**: the minimum `php` or `squizlabs/php_codesniffer` requirement was raised, or a sniff, category or
     error code was renamed or removed (codes are public API, used in users' `phpcs.xml` and in
     `modern-wpcs-ruleset/ModernWpcsRuleset/ruleset.xml`).
   - **Minor**: a new sniff, a new error code, or a new public sniff property.
   - **Patch**: bug fixes (false positives/negatives, fixer fixes), refactors, docs, CI.
   - Decide patch/minor yourself. For a **major**, tell the user why and get confirmation before tagging.
     (Pending at the time of writing: `php` went from `^8.2` to `^8.4` after `v2.0.0`, so the next release is `v3.0.0`.)

4. **Tag** the release commit with an **annotated** tag (repo convention since `v2.0.0`), leading `v`, three parts:
   ```powershell
   git tag -a vX.Y.Z -m @'
   Release vX.Y.Z: <one-line summary>

   - <notable change>
   - <notable change>
   '@
   ```
   Keep the body to one line per user-visible change: sniffs fixed or added, and requirement changes.
   Don't restate the diff. Don't mention the AI agent.

5. **Push** the branch first (if it has unpushed commits), then **only this tag**:
   ```
   git push origin master
   git push origin vX.Y.Z
   ```
   **Never** `git push --tags`: the clone can hold tags from the `upstream` remote. Never push to `upstream`.
   Don't use `--force`. If the branch push is rejected, `git pull --rebase origin master`, re-run step 2,
   then move the still-unpushed tag (`git tag -d vX.Y.Z` and re-create it) and push again.

6. **Report**: the tag, its commit hash, the previous → new version, and the summary.
   - Remind the user about downstream. `modern-wpcs-ruleset` requires `contactjavas/modern-wpcs-standard ^2.0.0`.
     A **major** release needs a constraint bump there (see that repo's `release` skill and AGENTS.md
     "Upstream Dependencies"). A minor or patch release reaches it with `composer update contactjavas/modern-wpcs-standard`.
   - A GitHub Release page is optional (the latest one dates from 2021). Create one with
     `gh release create vX.Y.Z --notes-from-tag` only if the user asks.

## Don'ts

- Don't add a `version` field to `composer.json`. Composer takes the version from the tag.
- Don't skip hooks (`--no-verify`) or amend commits that were already pushed.
- Don't move, delete or re-push a tag that already exists on `origin`. Release the next version instead.
