import { test, expect } from '@playwright/test';

test.describe('Admin Panel & Developer Tools Workflow', () => {
  test('admin can access and navigate Admin Panel sub-routes', async ({ page }) => {
    await page.goto('/auth/dev-login/admin');
    await page.waitForURL('**/dashboard');

    // Navigate to Users & Roles
    await page.goto('/admin/users');
    await expect(page.locator('body')).toContainText('User & Role Management');
    await expect(page.locator('body')).toContainText('User Details');
    await expect(page.locator('body')).toContainText('Chairperson Status');

    // Navigate to Departments
    await page.goto('/admin/departments');
    await expect(page.locator('body')).toContainText('University Departments');
    await expect(page.locator('body')).toContainText('Add New School / Department');

    // Navigate to Document Types & Templates
    await page.goto('/admin/document-types');
    await expect(page.locator('body')).toContainText('Document Types & Templates');
    await expect(page.locator('body')).toContainText('Add New Required Document Type');

    // Navigate to Archive Queue
    await page.goto('/admin/archive');
    await expect(page.locator('body')).toContainText('Archive Queue');
    await expect(page.locator('body')).toContainText('Control No.');

    // Navigate to Audit Log
    await page.goto('/admin/audit-log');
    await expect(page.locator('body')).toContainText('System Audit Log');
  });

  test('developer can access Developer Operational Panel tabs and controls', async ({ page }) => {
    await page.goto('/auth/dev-login/developer');
    await page.waitForURL('**/dashboard');

    await page.goto('/admin/dev');

    await expect(page.locator('body')).toContainText('Developer Console & Operations');
    await expect(page.locator('body')).toContainText('System Info');
    await expect(page.locator('body')).toContainText('Queue Monitor');
    await expect(page.locator('body')).toContainText('Control Number Counters');
  });
});
