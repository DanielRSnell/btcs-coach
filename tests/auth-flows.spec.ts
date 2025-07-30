import { test, expect } from '@playwright/test';

const BASE_URL = 'http://btcs-coach.test';

test.describe('Authentication Flows', () => {
  test.beforeEach(async ({ page }) => {
    // Set viewport for consistent testing
    await page.setViewportSize({ width: 1280, height: 720 });
  });

  test('should display login form with animations', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    
    // Check page title and heading
    await expect(page).toHaveTitle(/Log in/);
    await expect(page.locator('h1')).toContainText('Log in to your account');
    
    // Check form elements are present
    await expect(page.getByRole('textbox', { name: 'Email address' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Password' })).toBeVisible();
    await expect(page.getByRole('checkbox', { name: 'Remember me' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Log in' })).toBeVisible();
    
    // Check navigation link to register
    await expect(page.getByRole('link', { name: 'Sign up' })).toBeVisible();
  });

  test('should display registration form with animations', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);
    
    // Check page title and heading
    await expect(page).toHaveTitle(/Register/);
    await expect(page.locator('h1')).toContainText('Create an account');
    
    // Check form elements are present
    await expect(page.getByRole('textbox', { name: 'Name' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Email address' })).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
    await expect(page.locator('#password_confirmation')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Create account' })).toBeVisible();
    
    // Check navigation link to login
    await expect(page.getByRole('link', { name: 'Log in' })).toBeVisible();
  });

  test('should successfully register a new user', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);
    
    // Fill out registration form
    const timestamp = Date.now();
    const testEmail = `test${timestamp}@btcs.com`;
    
    await page.getByRole('textbox', { name: 'Name' }).fill('Test User');
    await page.getByRole('textbox', { name: 'Email address' }).fill(testEmail);
    await page.locator('#password').fill('password123');
    await page.locator('#password_confirmation').fill('password123');
    
    // Submit form
    await page.getByRole('button', { name: 'Create account' }).click();
    
    // Should redirect to dashboard or email verification
    await page.waitForURL(`${BASE_URL}/**`, { timeout: 10000 });
    
    // Check we're not on the register page anymore
    expect(page.url()).not.toBe(`${BASE_URL}/register`);
  });

  test('should successfully login with test user', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    
    // Fill out login form with test user credentials
    await page.getByRole('textbox', { name: 'Email address' }).fill('john@btcs.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    
    // Submit form
    await page.getByRole('button', { name: 'Log in' }).click();
    
    // Should redirect to dashboard
    await page.waitForURL(`${BASE_URL}/dashboard`, { timeout: 10000 });
    
    // Check we're on the dashboard page
    expect(page.url()).toBe(`${BASE_URL}/dashboard`);
    await expect(page.locator('h1')).toContainText('Dashboard');
  });

  test('should show validation errors for invalid login', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    
    // Try to submit with invalid credentials
    await page.getByRole('textbox', { name: 'Email address' }).fill('invalid@email.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('wrongpassword');
    await page.getByRole('button', { name: 'Log in' }).click();
    
    // Should stay on login page and show errors
    await expect(page.getByText('These credentials do not match our records.')).toBeVisible();
  });

  test('should show validation errors for invalid registration', async ({ page }) => {
    await page.goto(`${BASE_URL}/register`);
    
    // Try to submit with mismatched passwords
    await page.getByRole('textbox', { name: 'Name' }).fill('Test User');
    await page.getByRole('textbox', { name: 'Email address' }).fill('test@example.com');
    await page.locator('#password').fill('password123');
    await page.locator('#password_confirmation').fill('differentpassword');
    await page.getByRole('button', { name: 'Create account' }).click();
    
    // Should stay on register page and show errors
    await expect(page.locator('.text-destructive')).toBeVisible();
  });

  test('should navigate between login and register pages', async ({ page }) => {
    // Start on login page
    await page.goto(`${BASE_URL}/login`);
    await expect(page.locator('h1')).toContainText('Log in to your account');
    
    // Click sign up link
    await page.getByRole('link', { name: 'Sign up' }).click();
    await page.waitForURL(`${BASE_URL}/register`);
    await expect(page.locator('h1')).toContainText('Create an account');
    
    // Click log in link
    await page.getByRole('link', { name: 'Log in' }).click();
    await page.waitForURL(`${BASE_URL}/login`);
    await expect(page.locator('h1')).toContainText('Log in to your account');
  });

  test('should have responsive design on mobile', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    
    await page.goto(`${BASE_URL}/login`);
    
    // Check that form is still visible and usable on mobile
    await expect(page.getByRole('textbox', { name: 'Email address' })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Password' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Log in' })).toBeVisible();
    
    // Form should be full width on mobile
    const form = page.locator('form');
    const formWidth = await form.boundingBox();
    expect(formWidth?.width).toBeGreaterThan(300);
  });
});