import { test, expect } from '@playwright/test';

test.describe('Student Portal & Request Workflow', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/auth/dev-login/student');
    await page.waitForURL('**/dashboard');
  });

  test('student dashboard renders header, new request button, and sidebar', async ({ page }) => {
    await expect(page.locator('body')).toContainText('My Ethics Submissions');

    // Verify New Request button
    const newRequestBtn = page.getByRole('link', { name: /New Ethics Request/i }).first();
    await expect(newRequestBtn).toBeVisible();
    await expect(newRequestBtn).toHaveAttribute('href', '/requests/create');
  });

  test('student can navigate to create ethics request and see form controls', async ({ page }) => {
    await page.goto('/requests/create');

    await expect(page.locator('body')).toContainText('Submit Ethics Review Requirement');
    await expect(page.locator('body')).toContainText('Research Study Details');
    await expect(page.locator('body')).toContainText('Full Title of Research');

    // Check submit action button
    const submitBtn = page.getByRole('button', { name: /Submit Ethics Request/i });
    await expect(submitBtn).toBeVisible();
  });

  test('FAQ and Ethical Considerations page renders compliance guidelines', async ({ page }) => {
    await page.goto('/faq');

    await expect(page.locator('body')).toContainText("Submission Do's & Don'ts");
    await expect(page.locator('body')).toContainText('THINGS TO DO');
    await expect(page.locator('body')).toContainText('THINGS NOT TO DO');
    await expect(page.locator('body')).toContainText('Mandatory Sections Needed in Chapter 2');
    await expect(page.locator('body')).toContainText('Risk-benefit assessment to respondents');
  });
});
