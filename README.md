# Guide on How to Use Git

## GitHub Version Control Best Practices
- **Pull from main before starting a feature** – Avoid merge conflicts by staying updated.
- **Use a consistent branching strategy** – Example: `feature/*`, `bugfix/*`, `hotfix/*`, `release/*`.
- **Write meaningful commits** – Keep changes small and use clear commit messages.
- **Always use Pull Requests (PRs)** – No direct pushes to `main`, request reviews before merging.
- **Keep branches updated** – Regularly merge or rebase `main` into your branch.
- **Use .gitignore** – Exclude unnecessary or sensitive files.
- **Delete merged branches** – Keep the repository clean.
- **Use descriptive branch names** – Follow the naming conventions to indicate purpose.
- **Write clear PR descriptions** – Explain what the changes do and reference relevant issues.
- **Avoid committing directly to a shared branch** – Always work on a separate feature branch.
- **Use Git stash to save work-in-progress** – `git stash` helps when switching branches temporarily.
- **Review diffs before committing** – Use `git diff` to check changes before adding them.

## Naming Convention
- **Feature Branches**: For new features, use the prefix `feature/`. For example, `feature/authentication`.
- **Bugfix Branches**: For code corrections, start with `bugfix/`. For instance, `bugfix/login-error`.
- **Hotfix Branches**: For critical fixes that need immediate attention, use `hotfix/`. For example, `hotfix/security-patch`.
- **Release Branches**: For preparing a new production release, use `release/`. For instance, `release/v1.2.0`.
