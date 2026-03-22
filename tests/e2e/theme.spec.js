const { test, expect } = require('@playwright/test');

test.describe('Theme basics', () => {
	test('homepage loads with 200', async ({ page }) => {
		const response = await page.goto('/');
		expect(response.status()).toBe(200);
	});

	test('theme stylesheet is loaded', async ({ page }) => {
		await page.goto('/');
		const stylesheet = page.locator('#sovereignty-style-css');
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

test.describe('Single post', () => {
	test('standard post renders with entry structure', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		await expect(page.locator('.entry-header')).toBeAttached();
		await expect(page.locator('.entry-content')).toBeAttached();
		await expect(page.locator('.entry-footer')).toBeAttached();
	});

	test('post has microformats2 markup', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		await expect(page.locator('.h-entry').first()).toBeAttached();
		await expect(page.locator('.e-content').first()).toBeAttached();
		await expect(page.locator('.p-name').first()).toBeAttached();
		await expect(page.locator('.dt-published').first()).toBeAttached();
	});

	test('post has Schema.org microdata', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		const body = page.locator('body');
		await expect(body).toHaveAttribute('itemtype', /BlogPosting/);
	});

	test('author byline has h-card', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		await expect(page.locator('.entry-header .h-card').first()).toBeAttached();
		await expect(page.locator('.entry-header .p-author').first()).toBeAttached();
	});

	test('comment section renders', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		await expect(page.locator('#comments')).toBeAttached();
		// Seeded content has 1 comment.
		await expect(page.locator('.comment').first()).toBeAttached();
	});

	test('featured image renders', async ({ page }) => {
		await page.goto('/welcome-to-sovereignty/');
		// This post has a featured image assigned.
		const img = page.locator('article img');
		const count = await img.count();
		expect(count).toBeGreaterThan(0);
	});
});

test.describe('Post formats', () => {
	test('aside format renders without title heading', async ({ page }) => {
		await page.goto('/quick-aside/');
		// Aside format should not show a title heading (h1/h2 with entry-title).
		await expect(page.locator('h1.entry-title, h2.entry-title')).not.toBeAttached();
	});

	test('quote format renders', async ({ page }) => {
		await page.goto('/on-the-web/');
		await expect(page.locator('blockquote')).toBeAttached();
	});

	test('link format loads', async ({ page }) => {
		const response = await page.goto('/indieweb/');
		expect(response.status()).toBe(200);
	});

	test('status format loads', async ({ page }) => {
		const response = await page.goto('/status-update/');
		expect(response.status()).toBe(200);
	});

	test('image format loads', async ({ page }) => {
		const response = await page.goto('/sunset-beach/');
		expect(response.status()).toBe(200);
	});

	test('video format loads', async ({ page }) => {
		const response = await page.goto('/big-buck-bunny/');
		expect(response.status()).toBe(200);
	});

	test('audio format loads', async ({ page }) => {
		const response = await page.goto('/podcast-episode/');
		expect(response.status()).toBe(200);
	});

	test('gallery format loads', async ({ page }) => {
		const response = await page.goto('/photo-gallery/');
		expect(response.status()).toBe(200);
	});

	test('chat format loads', async ({ page }) => {
		const response = await page.goto('/development-chat/');
		expect(response.status()).toBe(200);
	});
});

test.describe('Pages', () => {
	test('about page renders', async ({ page }) => {
		const response = await page.goto('/about/');
		expect(response.status()).toBe(200);
		await expect(page.locator('.entry-content')).toBeAttached();
	});

	test('now page renders', async ({ page }) => {
		const response = await page.goto('/now/');
		expect(response.status()).toBe(200);
		// page-now.php adds h-now class to main.
		await expect(page.locator('.h-now')).toBeAttached();
	});
});

test.describe('Archives', () => {
	test('category archive loads', async ({ page }) => {
		const response = await page.goto('/category/technology/');
		expect(response.status()).toBe(200);
		await expect(page.locator('.page-banner')).toBeAttached();
	});

	test('search results render', async ({ page }) => {
		const response = await page.goto('/?s=sovereignty');
		expect(response.status()).toBe(200);
		await expect(page.locator('#page-title')).toBeAttached();
	});
});

test.describe('Semantic markup', () => {
	test('homepage body has Schema.org attributes', async ({ page }) => {
		await page.goto('/');
		const body = page.locator('body');
		await expect(body).toHaveAttribute('itemscope', '');
		await expect(body).toHaveAttribute('itemtype', /schema\.org/);
	});

	test('posts in listing have h-entry class', async ({ page }) => {
		await page.goto('/');
		const entries = page.locator('.h-entry');
		const count = await entries.count();
		expect(count).toBeGreaterThan(0);
	});
});

test.describe('Error pages', () => {
	test('404 page renders correctly', async ({ page }) => {
		const response = await page.goto('/this-page-does-not-exist/');
		expect(response.status()).toBe(404);
		await expect(page.locator('body.error404')).toBeAttached();
	});
});

test.describe('PWA', () => {
	test('manifest endpoint returns JSON', async ({ page }) => {
		const response = await page.goto('/?sovereignty_manifest=1');
		expect(response.status()).toBe(200);
		const contentType = response.headers()['content-type'];
		expect(contentType).toContain('manifest+json');
	});
});

test.describe('Accessibility', () => {
	test('page has valid lang attribute', async ({ page }) => {
		await page.goto('/');
		await expect(page.locator('html')).toHaveAttribute(
			'lang',
			/^[a-z]{2}(-[A-Za-z]+)*$/,
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
