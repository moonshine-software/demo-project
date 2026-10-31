import { test, expect } from '@playwright/test';

test('unknown pages render the MoonShine error page', async ({ page }) => {
    const response = await page.goto('/admin/nonexistent-page');
    expect(response.status()).toBe(404);
    await expect(page.getByRole('heading', { name: '404', exact: true })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Back', exact: true })).toHaveAttribute('href', /\/admin$/);
});
