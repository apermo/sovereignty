#!/bin/bash
# Claude Code PreToolUse hook: runs linting before git commit

INPUT=$(cat)
COMMAND=$(echo "$INPUT" | jq -r '.tool_input.command // empty')

# Only intercept git commit commands
if ! [[ "$COMMAND" =~ git[[:space:]]+(commit|add[[:space:]]+.*&&[[:space:]]*git[[:space:]]+commit) ]]; then
  exit 0
fi

cd "$CLAUDE_PROJECT_DIR" || exit 0

ERRORS=()

echo "Running linters before commit..." >&2

# PHP linting (only if PHP files exist in staging)
if command -v vendor/bin/phpcs &> /dev/null; then
  vendor/bin/phpcs --report=summary 2>&1 >&2
  if [ $? -ne 0 ]; then
    ERRORS+=("PHPCS found errors")
  fi
fi

# PHPStan
if command -v vendor/bin/phpstan &> /dev/null; then
  vendor/bin/phpstan analyse --no-progress --error-format=table --memory-limit=2G 2>&1 >&2
  if [ $? -ne 0 ]; then
    ERRORS+=("PHPStan found errors")
  fi
fi

# ESLint
if command -v npx &> /dev/null && [ -f .eslintrc.json ]; then
  npx --no eslint 'assets/js/**/*.js' 2>&1 >&2
  if [ $? -ne 0 ]; then
    ERRORS+=("ESLint found errors")
  fi
fi

# Stylelint
if command -v npx &> /dev/null && [ -f .stylelintrc.json ]; then
  npx --no stylelint 'assets/sass/**/*.scss' 2>&1 >&2
  if [ $? -ne 0 ]; then
    ERRORS+=("Stylelint found errors")
  fi
fi

if [ ${#ERRORS[@]} -gt 0 ]; then
  echo "" >&2
  echo "BLOCKED: Linting failed. Fix these before committing:" >&2
  for err in "${ERRORS[@]}"; do
    echo "  - $err" >&2
  done
  exit 2
fi

echo "All linters passed." >&2
exit 0