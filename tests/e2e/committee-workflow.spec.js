import { test, expect } from '@playwright/test';

test.describe('Committee Review Queue Workflow', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/auth/dev-login/committee');
    await page.waitForURL('**/dashboard');
  });

  test('committee dashboard renders Review Queue table and filters', async ({ page }) => {
    await expect(page.locator('body')).toContainText('Review Queue');

    // Verify filter select controls
    await expect(page.locator('select').first()).toBeVisible();

    // Verify search input within review queue
    const queueSearch = page.getByPlaceholder(/Search title, student/i);
    await expect(queueSearch).toBeVisible();
  });
});
