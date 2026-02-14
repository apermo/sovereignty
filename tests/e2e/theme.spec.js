const { test, expect } = require('@playwright/test');

test.describe('Theme basics', () => {
	test('homepage loads with 200', async ({ page }) => {
		const response = await page.goto('/');
		expect(response.status()).toBe(200);
	});

	test('theme stylesheet is loaded', async ({ page }) => {
		await page.goto('/');
		const stylesheet = page.locator('#autonomie-style-css');
		await expect(stylesheet).toBeAttached();
	});

	test('site title is visible', async ({ page }) => {
		await page.goto('/');
		const siteTitle = page.locator('#site-title');
		await expect(siteTitle).toBeVisible();
	});

	test('navigation menu is visible', async ({ page }) => {
		await page.goto('/');
		const nav = page.locator('#site-navigation');
		await expect(nav).toBeVisible();
	});

	test('search form is visible', async ({ page }) => {
		await page.goto('/');
		const search = page.locator('input[type="search"]');
		await expect(search).toBeVisible();
	});

	test('skip-to-content link targets main content', async ({ page }) => {
		await page.goto('/');
		const skipLink = page.locator('.skip-link a[href="#primary"]');
		await expect(skipLink).toBeAttached();
		const target = page.locator('#primary');
		await expect(target).toBeAttached();
	});
});

test.describe('Error pages', () => {
	test('404 page renders correctly', async ({ page }) => {
		const response = await page.goto('/this-page-does-not-exist/');
		expect(response.status()).toBe(404);
		await expect(page.locator('body.error404')).toBeAttached();
	});
});

test.describe('Accessibility', () => {
	test('page has valid lang attribute', async ({ page }) => {
		await page.goto('/');
		await expect(page.locator('html')).toHaveAttribute(
			'lang',
			/^[a-z]{2}(-[A-Za-z]+)*$/
		);
	});

	test('images have alt attributes', async ({ page }) => {
		await page.goto('/');
		const images = page.locator('img');
		const count = await images.count();
		expect(count).toBeGreaterThan(0);
		for (let i = 0; i < count; i++) {
			const alt = await images.nth(i).getAttribute('alt');
			expect(alt).not.toBeNull();
		}
	});
});
