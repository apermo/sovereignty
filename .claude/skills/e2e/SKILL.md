---
name: e2e
description: Write Playwright E2E tests for the sovereignty WordPress theme. Use when asked to write, add, update, or fix E2E tests.
argument-hint: "[what to test]"
allowed-tools: Read, Grep, Glob, Bash(npx playwright *), Bash(ddev exec *), Write, Edit
---

# Playwright E2E Test Expert

You are an expert in writing Playwright E2E tests for a WordPress theme. Your job is to write reliable, maintainable E2E tests for the sovereignty theme.

## Project context

- **Test directory**: `tests/e2e/`
- **Config**: `playwright.config.js`
- **Base URL**: `https://sovereignty.ddev.site` (DDEV local dev)
- **Run tests**: `npx playwright test`
- **Run single file**: `npx playwright test tests/e2e/<file>.spec.js`
- **Debug mode**: `npx playwright test --ui`
- **Theme**: WordPress theme (Autonomie fork) with microformats2, IndieWeb support

## Before writing tests

1. **Always read existing tests first** to understand patterns and avoid duplication:
   - `tests/e2e/theme.spec.js` — baseline theme tests
   - Any other `tests/e2e/*.spec.js` files

2. **Inspect the actual markup** before writing selectors. Never guess CSS selectors. Run:
   ```bash
   ddev exec "curl -s -H 'Host: sovereignty.ddev.site' 'http://127.0.0.1:80/'" | grep -i '<pattern>'
   ```
   Or use the Playwright MCP tools to take a screenshot and inspect.

3. **Check theme template files** to understand the HTML structure:
   - `header.php` — site header, navigation, search
   - `footer.php` — site footer
   - `index.php` — main loop
   - `single.php` — single post
   - `archive.php` — archive pages
   - `404.php` — error page
   - `template-parts/` — reusable parts
   - `templates/` — post format templates (content-*.php)

## Code style rules (enforced by ESLint with WordPress standard)

- Use **tabs** for indentation (not spaces)
- Use `require` not `import` (CommonJS, no ESM)
- **No spaces inside parentheses for function calls**: `toBe(200)` not `toBe( 200 )`
- **No spaces inside argument destructuring**: `({ page })` not `( { page } )`
- **Spaces inside parentheses for control structures**: `for ( let i = 0; ...` and `if ( condition )`
- Use single quotes for strings
- Always end statements with semicolons

Example of correct formatting:
```javascript
const { test, expect } = require('@playwright/test');

test.describe('Feature name', () => {
	test('test description', async ({ page }) => {
		await page.goto('/');
		const element = page.locator('#my-element');
		await expect(element).toBeVisible();
	});
});
```

## Test writing guidelines

### Structure
- Group related tests in `test.describe()` blocks
- One spec file per feature area (e.g., `navigation.spec.js`, `darkmode.spec.js`)
- Test names should describe the expected behavior, not the implementation

### Selectors (in order of preference)
1. **Semantic selectors**: `page.locator('nav')`, `page.locator('search')`
2. **IDs**: `page.locator('#site-navigation')`
3. **ARIA roles**: `page.getByRole('navigation')`
4. **Data attributes**: `page.locator('[data-testid="..."]')`
5. **CSS classes**: `page.locator('.site-header')` (last resort — fragile)

Never use:
- XPath selectors
- Complex CSS chains (`.foo > .bar > .baz`)
- Index-based selectors unless iterating a collection

### Assertions
- Use `toBeVisible()` for elements the user should see
- Use `toBeAttached()` for elements in the DOM but possibly hidden (e.g., `<link>` tags, skip links)
- Use `toHaveAttribute()` to check specific attribute values
- Use `toHaveCSS()` for visual style checks (dark mode, responsive)
- Use `toHaveCount()` for collection length checks

### Responsive testing
```javascript
test('mobile menu toggle appears on narrow viewport', async ({ page }) => {
	await page.setViewportSize({ width: 375, height: 667 });
	await page.goto('/');
	// test mobile behavior
});
```

### Dark mode testing
```javascript
test('respects dark color scheme preference', async ({ page }) => {
	await page.emulateMedia({ colorScheme: 'dark' });
	await page.goto('/');
	// check dark mode styles
});
```

### Testing WordPress-specific things
- **Post content**: Create posts via WP-CLI before testing: `ddev wp post create --post_title="Test" --post_status=publish`
- **Menus**: Check nav exists but don't hardcode menu item text (it's user-configurable)
- **Widgets**: Check sidebar structure, not specific widget content
- **Search**: Test the form submission flow, verify results page loads

### What NOT to test
- WordPress admin UI (not our code)
- Third-party plugin output
- Exact text content that comes from the database
- Pixel-perfect positioning

## After writing tests

1. **Run the tests**: `npx playwright test`
2. **Run ESLint**: `npx eslint tests/e2e/`
3. **Fix lint issues**: `npx eslint --fix tests/e2e/`
4. Report results — all tests must pass before committing

## Test for: $ARGUMENTS