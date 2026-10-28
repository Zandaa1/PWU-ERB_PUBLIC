import { test, expect } from '@playwright/test';

test.describe('Landing Page & Authentication Flow', () => {
  test.beforeEach(async ({ context }) => {
    await context.clearCookies();
  });

  test('unauthenticated user sees institutional DEMO login portal', async ({ page }) => {
    await page.goto('/');

    // Validate institution branding and typography
    await expect(page).toHaveTitle(/DEMO|Ethics/i);
    await expect(page.locator('body')).toContainText("Example Research Institution");
    await expect(page.locator('body')).toContainText('Ethics Review Board Portal');
    await expect(page.locator('body')).toContainText('Thesis & Research Ethics');

    // Validate Google Sign-In action
    const loginBtn = page.getByRole('link', { name: /Sign in with Google/i });
    await expect(loginBtn).toBeVisible();
    await expect(loginBtn).toHaveAttribute('href', /\/auth\/google/);

    // Validate guidance text
    await expect(page.locator('body')).toContainText('Use your official');
    await expect(page.locator('body')).toContainText('@example.test');
  });

  test('displays domain error warning when redirected with unauthorized domain', async ({ page }) => {
    await page.goto('/?error=unauthorized_domain');

    await expect(page.locator('body')).toContainText(/Please use your school Google account/i);
  });

  test('can authenticate via developer bypass and access dashboard', async ({ page }) => {
    // Navigate to dev login for student
    await page.goto('/auth/dev-login/student');
    await page.waitForURL('**/dashboard');

    // Verify redirected to dashboard with student view
    await expect(page.locator('body')).toContainText('My Ethics Submissions');
    await expect(page.locator('body')).toContainText(/Student/i);
  });
});
