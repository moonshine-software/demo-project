import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { readFileSync } from 'node:fs';

// Read the registered pages so newly added resources are covered automatically.
const registered = (command) => JSON.parse(execFileSync('php', ['artisan', command, '--json'], {
    encoding: 'utf8',
    env: { ...process.env, APP_CONFIG_CACHE: '/tmp/moonshine-e2e-no-config.php', MOONSHINE_ROUTE_PREFIX: 'admin' },
}));
const resources = registered('moonshine:resources');
const pages = registered('moonshine:pages').filter((page) =>
    !page.class.endsWith('\\LoginPage') && !page.class.endsWith('\\ErrorPage'));

const resourceContent = {
    ArticleResource: 'E2E article',
    CategoryResource: 'E2E category',
    CommentResource: 'E2E comment',
    DictionaryResource: 'E2E dictionary',
    MoonShineUserResource: 'admin@moonshine-laravel.com',
    MoonShineUserRoleResource: 'Admin',
    TaskResource: 'E2E task',
    UserResource: 'E2E user',
};

test.beforeEach(async ({ page, baseURL }) => {
    const errors = [];
    page.downloadUrls = new Set();
    page.on('download', (download) => page.downloadUrls.add(download.url()));
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('console', (message) => {
        if (message.type() === 'error') errors.push(message.text());
    });
    page.on('response', (response) => {
        if (response.url().startsWith(baseURL) && response.status() >= 400) {
            errors.push(`${response.status()} ${response.url()}`);
        }
    });
    page.on('requestfailed', (request) => {
        if (request.url().startsWith(baseURL)) errors.push(`${request.failure()?.errorText} ${request.url()}`);
    });
    page.browserErrors = errors;
});

test.afterEach(async ({ page }) => {
    const errors = page.browserErrors.filter((error) =>
        ![...page.downloadUrls].some((url) => error === `net::ERR_ABORTED ${url}`));
    expect(errors, 'JavaScript, console and local HTTP errors').toEqual([]);
});

async function visit(page, path) {
    const response = await page.goto(path);
    expect(response.status()).toBe(200);
    await expect(page).toHaveURL(new URL(path, 'http://127.0.0.1:8005').href);
    await expect(page.locator('body')).toBeVisible();
    await page.waitForLoadState('networkidle');
}

async function login(page) {
    await visit(page, '/admin/login');
    await page.locator('[name="username"]').fill('admin@moonshine-laravel.com');
    await page.locator('[name="password"]').fill('moonshine');
    await page.locator('.authentication-form button[type="submit"]').click();
    await expect(page).toHaveURL('http://127.0.0.1:8005/admin');
    await expect(page.getByRole('heading', { name: 'Dashboard', exact: true })).toBeVisible();
    await page.waitForLoadState('networkidle');
}

for (const [path, content] of [
    ['/articles', 'E2E article'],
    ['/articles/e2e-article', 'E2E article'],
    ['/dictionaries', 'E2E dictionary'],
    ['/dictionaries/e2e-dictionary', 'E2E dictionary'],
]) {
    test(`public ${path}`, async ({ page }) => {
        await visit(page, path);
        await expect(page.locator('main')).toContainText(content);
    });
}

test('public home', async ({ page }) => {
    await visit(page, '/');
    await expect(page.getByRole('img', { name: 'YouTube CutCode' })).toBeVisible();
});

test('guests are redirected to login', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);
    await expect(page.locator('.authentication-form')).toBeVisible();
});

test('login rejects invalid credentials', async ({ page }) => {
    await visit(page, '/admin/login');
    await page.locator('[name="username"]').fill('admin@moonshine-laravel.com');
    await page.locator('[name="password"]').fill('incorrect-password');
    await page.locator('.authentication-form button[type="submit"]').click();
    await expect(page).toHaveURL(/\/admin\/login$/);
    await expect(page.locator('body')).toContainText('credentials');
});

test.describe('authenticated pages', () => {
    test.beforeEach(async ({ page }) => login(page));

    for (const path of ['/admin', ...pages.map((page) => new URL(page.url).pathname)]) {
        test(path, async ({ page }) => {
            await visit(page, path);
            await expect(page.locator('main h1').first()).toBeVisible();
            if (path.includes('profile')) {
                await expect(page.locator('[name="name"]')).toHaveValue('Admin');
                await expect(page.locator('[name="email"]')).toHaveValue('admin@moonshine-laravel.com');
            }
        });
    }

    for (const resource of resources) {
        for (const registeredPage of resource.pages) {
            const path = new URL(registeredPage.url).pathname;
            const isForm = registeredPage.class.endsWith('FormPage');
            const isDetail = registeredPage.class.endsWith('DetailPage');

            for (const suffix of isForm ? ['', '/1'] : [isDetail ? '/1' : '']) {
                test(`${path}${suffix}`, async ({ page }) => {
                    await visit(page, path + suffix);
                    await expect(page.locator('main h1').first()).toBeVisible();
                    if (isForm || path.endsWith('/setting-page')) {
                        await expect(page.locator('main form').first()).toBeVisible();
                        await expect(page.locator('main input:not([type="hidden"])').first()).toBeVisible();
                    } else {
                        const content = resourceContent[resource.class.split('\\').pop()];
                        expect(content, `Add a content assertion for ${resource.class}`).toBeTruthy();
                        await expect(page.locator('main')).toContainText(content);
                    }
                });
            }
        }
    }

    test('dashboard asynchronous tabs', async ({ page }) => {
        await expect(page.getByRole('heading', { name: 'Подписчики', exact: true })).toBeVisible();
        await page.getByRole('link', { name: 'Table', exact: true }).click();
        await expect(page.locator('.async-table')).toBeVisible();
        await expect(page.locator('.async-table tbody tr')).toHaveCount(3);
        await page.getByRole('link', { name: 'Metrics', exact: true }).click();
        await expect(page.getByRole('heading', { name: 'Подписчики', exact: true })).toBeVisible();
    });

    test('user create, edit and delete through the browser', async ({ page }) => {
        const prefix = '/admin/resource/user-resource';
        await visit(page, `${prefix}/user-form-page`);
        for (const [name, value] of Object.entries({
            name: 'Browser CRUD user', email: 'browser-crud@example.com',
            password: 'Password123!', password_repeat: 'Password123!',
        })) {
            await page.locator(`main [name="${name}"]`).fill(value);
        }
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        await expect(page).toHaveURL(/user-form-page\/\d+$/);
        const id = new URL(page.url()).pathname.split('/').pop();
        await expect(page.locator('main [name="name"]')).toHaveValue('Browser CRUD user');

        await page.locator('main [name="name"]').fill('Browser CRUD user updated');
        const saved = page.waitForResponse((response) =>
            response.url().endsWith(`/crud/${id}`) && response.request().method() === 'POST');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        expect((await saved).status()).toBe(200);
        await page.reload();
        await expect(page.locator('main [name="name"]')).toHaveValue('Browser CRUD user updated');

        await page.locator(`main a[href*="/crud/${id}?"]`).click();
        await page.getByRole('button', { name: 'Confirm', exact: true }).click();
        await expect(page).toHaveURL(new URL(`${prefix}/user-index-page`, 'http://127.0.0.1:8005').href);
        await expect(page.locator('main')).not.toContainText('Browser CRUD user updated');
    });

    test('category CRUD in asynchronous modals', async ({ page }) => {
        const prefix = '/admin/resource/category-resource';
        await visit(page, `${prefix}/category-index-page`);
        await page.getByRole('link', { name: 'Create', exact: true }).click();
        const form = page.locator('form:visible').filter({ has: page.locator('[name="title"]') });
        await form.locator('[name="title"]').fill('Browser modal category');
        await form.getByRole('button', { name: 'Save', exact: true }).click();
        await expect(page.locator('main')).toContainText('Browser modal category');
        await page.reload();
        await expect(page.locator('main')).toContainText('Browser modal category');
        const item = page.locator('li.tree__item').filter({ hasText: 'Browser modal category' });
        const id = await item.getAttribute('data-id');
        await item.locator(`a[href*="/category-form-page/${id}?"]`).click();
        await form.locator('[name="title"]').fill('Browser modal category updated');
        await form.getByRole('button', { name: 'Save', exact: true }).click();
        await expect(page.locator('main')).toContainText('Browser modal category updated');
        await page.reload();
        await expect(item).toContainText('Browser modal category updated');
        await item.locator(`a[href$="/crud/${id}"]`).click();
        await page.getByRole('button', { name: 'Confirm', exact: true }).click();
        await expect(page.locator('main')).not.toContainText('Browser modal category updated');
    });

    test('settings persist after saving', async ({ page }) => {
        await visit(page, '/admin/resource/setting-resource/setting-page');
        const input = page.locator('main [name="email"]');
        const original = await input.inputValue();
        await input.fill('browser-settings@example.com');
        const saved = page.waitForResponse((response) =>
            response.url().includes('/setting-resource/crud/1') && response.request().method() === 'POST');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        expect((await saved).status()).toBe(200);
        await page.reload();
        await expect(input).toHaveValue('browser-settings@example.com');
        await input.fill(original);
        const restored = page.waitForResponse((response) =>
            response.url().includes('/setting-resource/crud/1') && response.request().method() === 'POST');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        expect((await restored).status()).toBe(200);
    });

    test('profile changes persist', async ({ page }) => {
        await visit(page, '/admin/page/profile-page');
        const name = page.locator('main [name="name"]');
        await name.fill('Browser Admin');
        const saved = page.waitForResponse((response) =>
            new URL(response.url()).pathname === '/admin/profile' && response.request().method() === 'POST');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        expect((await saved).status()).toBe(200);
        await page.reload();
        await expect(name).toHaveValue('Browser Admin');
        await name.fill('Admin');
        const restored = page.waitForResponse((response) =>
            new URL(response.url()).pathname === '/admin/profile' && response.request().method() === 'POST');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        expect((await restored).status()).toBe(200);
    });

    test('article XLSX export and import round trip', async ({ page }) => {
        await visit(page, '/admin/resource/article-resource/article-index-page');
        const downloaded = page.waitForEvent('download');
        await page.getByRole('link', { name: 'Export', exact: true }).click();
        const download = await downloaded;
        expect(download.suggestedFilename()).toMatch(/\.xlsx$/);
        expect(await download.failure()).toBeNull();
        await page.getByRole('link', { name: 'Import', exact: true }).click();
        const file = page.locator('input[name="import_file"]');
        await file.setInputFiles({
            name: download.suggestedFilename(),
            mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            buffer: readFileSync(await download.path()),
        });
        await page.getByRole('button', { name: 'Confirm', exact: true }).click();
        await expect(page.locator('body')).toContainText('Imported');
        await expect(page.locator('main')).toContainText('E2E article');
    });

    test('article table and cards can be switched', async ({ page }) => {
        await visit(page, '/admin/resource/article-resource/article-index-page');
        await page.locator('main a[href*="value=cards"]').click();
        await expect(page.locator('.js-cards-builder-container')).toBeVisible();
        await expect(page.locator('.js-cards-builder-container')).toContainText('E2E article');
        await page.locator('main a[href*="value=list"]').click();
        await expect(page.locator('main table[data-name="index-table-article-resource"]')).toContainText('E2E article');
    });
});
