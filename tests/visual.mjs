import { chromium } from 'playwright';
import fs from 'node:fs';
fs.mkdirSync('test-results', { recursive: true });
const browser = await chromium.launch();
try {
    for (const [label, width, height] of [['desktop', 1440, 1000], ['mobile', 390, 844]]) {
        const page = await browser.newPage({ viewport: { width, height } });
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        for (const [name, path] of [['records', '/view.php'], ['form', '/index.php']]) {
            const response = await page.goto('http://127.0.0.1:8080' + path);
            if (response.status() !== 200) throw new Error(label + ' ' + name + ': unexpected status');
            await page.locator('h1').waitFor();
            const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
            if (overflow) throw new Error(label + ' ' + name + ': page overflows horizontally');
            await page.screenshot({ path: 'test-results/' + label + '-' + name + '.png', fullPage: true });
        }
        await page.goto('http://127.0.0.1:8080/index.php');
        await page.getByLabel('Full name', { exact: true }).fill('Browser Test Student');
        await page.getByLabel('Student ID', { exact: true }).fill('007777');
        await page.getByLabel('Age', { exact: true }).fill('21');
        await page.getByLabel('Grade (%)', { exact: true }).fill('86.5');
        await page.getByLabel('Gender', { exact: true }).selectOption('other');
        await page.getByRole('button', { name: 'Add student', exact: true }).click();
        await page.getByText('Student added successfully.').waitFor();
        await page.getByRole('link', { name: 'Edit Browser Test Student', exact: true }).click();
        await page.getByLabel('Grade (%)', { exact: true }).fill('90');
        await page.getByRole('button', { name: 'Save changes', exact: true }).click();
        await page.getByRole('button', { name: 'Delete Browser Test Student', exact: true }).click();
        await page.getByRole('button', { name: 'Delete student', exact: true }).click();
        await page.getByText('Student deleted.', { exact: true }).waitFor();
        if (errors.length) throw new Error(errors.join('; '));
        await page.close();
    }
    console.log('Desktop/mobile layout and browser form checks passed.');
} finally {
    await browser.close();
}
