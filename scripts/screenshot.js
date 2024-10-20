// screenshot.mjs
import puppeteer from 'puppeteer';
import fs from 'fs';

(async () => {
    const url = process.argv[2]; // URL passed as argument
    const outputPath = process.argv[3]; // Path to save the screenshot

    try {
        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        await page.goto(url, { waitUntil: 'networkidle2' });

        // Capture the screenshot
        await page.screenshot({ path: outputPath, fullPage: true });
        await browser.close();

        console.log('Screenshot saved at ' + outputPath);
    } catch (error) {
        console.error('Error capturing screenshot:', error);
    }
})();
