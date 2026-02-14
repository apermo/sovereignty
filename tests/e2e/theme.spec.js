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

	test('navigation menu exists', async ({ page }) => {
		await page.goto('/');
		const nav = page.locator('#site-navigation');
		await expect(nav).toBeAttached();
	});

	test('search form is present', async ({ page }) => {
		await page.goto('/');
		const search = page.locator('input[type="search"]');
		await expect(search).toBeAttached();
	});

	test('skip-to-content link exists', async ({ page }) => {
		await page.goto('/');
		const skipLink = page.locator('.skip-link a[href="#content"]');
		await expect(skipLink).toBeAttached();
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
	test('page has lang attribute', async ({ page }) => {
		await page.goto('/');
		const lang = await page.locator('html').getAttribute('lang');
		expect(lang).toBeTruthy();
	});

	test('images have alt attributes', async ({ page }) => {
		await page.goto('/');
		const images = page.locator('img');
		const count = await images.count();
		for (let i = 0; i < count; i++) {
			const alt = await images.nth(i).getAttribute('alt');
			expect(alt).not.toBeNull();
		}
	});
});
