/**
 * Extract critical CSS using puppeteer-core + local Chrome.
 * Combines desktop + mobile viewports into one merged CSS.
 *
 * Usage: node scripts/extract-critical.mjs <url> [outputFile]
 */

import { launch } from 'puppeteer-core';
import { writeFileSync } from 'fs';

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

const url = process.argv[2] || 'https://cdtcms.ctizen.id/';
const outFile = process.argv[3] || null;

const viewports = [
  { name: 'desktop', width: 1300, height: 900 },
  { name: 'mobile',  width: 414,  height: 896 },
];

async function extractCriticalCss(url, vp) {
  const browser = await launch({
    executablePath: CHROME_PATH,
    headless: 'shell',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  const page = await browser.newPage();
  await page.setViewport({ width: vp.width, height: vp.height });

  // Start CSS coverage
  await page.coverage.startCSSCoverage();

  await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });

  // Wait a bit for any lazy-loaded styles
  await new Promise(r => setTimeout(r, 1500));

  const cssCoverage = await page.coverage.stopCSSCoverage();

  // Collect used CSS ranges (above-the-fold via coverage)
  let usedCss = '';
  for (const entry of cssCoverage) {
    for (const range of entry.ranges) {
      usedCss += entry.text.slice(range.start, range.end) + '\n';
    }
  }

  // Also extract computed styles for above-the-fold elements
  const criticalRules = await page.evaluate((viewportHeight) => {
    const sheets = [...document.styleSheets];
    const criticalSelectors = new Set();

    // Find all elements in the viewport
    const allElements = document.querySelectorAll('*');
    for (const el of allElements) {
      const rect = el.getBoundingClientRect();
      if (rect.top < viewportHeight && rect.bottom > 0 && rect.width > 0 && rect.height > 0) {
        // This element is in the viewport
        const tag = el.tagName.toLowerCase();
        if (el.id) criticalSelectors.add('#' + el.id);
        for (const cls of el.classList) {
          criticalSelectors.add('.' + cls);
        }
        criticalSelectors.add(tag);
      }
    }

    // Now collect all CSS rules that match critical selectors
    const rules = [];
    for (const sheet of sheets) {
      try {
        const cssRules = sheet.cssRules || sheet.rules;
        for (const rule of cssRules) {
          if (rule.type === CSSRule.STYLE_RULE) {
            // Check if any critical selector matches
            const selectorText = rule.selectorText;
            if (selectorText) {
              const selParts = selectorText.split(',').map(s => s.trim());
              for (const sel of selParts) {
                const found = [...criticalSelectors].some(cs => sel.includes(cs));
                if (found) {
                  rules.push(rule.cssText);
                  break;
                }
              }
            }
          } else if (rule.type === CSSRule.MEDIA_RULE) {
            // Include @media rules that have matching selectors
            const mediaRules = [];
            for (const mRule of rule.cssRules) {
              if (mRule.type === CSSRule.STYLE_RULE && mRule.selectorText) {
                const selParts = mRule.selectorText.split(',').map(s => s.trim());
                for (const sel of selParts) {
                  const found = [...criticalSelectors].some(cs => sel.includes(cs));
                  if (found) {
                    mediaRules.push(mRule.cssText);
                    break;
                  }
                }
              }
            }
            if (mediaRules.length) {
              rules.push(`@media ${rule.conditionText} { ${mediaRules.join(' ')} }`);
            }
          } else if (rule.type === CSSRule.FONT_FACE_RULE) {
            rules.push(rule.cssText);
          } else if (rule.cssText && rule.cssText.startsWith('@keyframes')) {
            rules.push(rule.cssText);
          }
        }
      } catch (e) {
        // Cross-origin stylesheet, skip
      }
    }

    return rules;
  }, vp.height);

  await browser.close();

  return { usedCss, criticalRules };
}

console.error(`Extracting critical CSS from: ${url}`);
console.error(`Viewports: ${viewports.map(v => `${v.name} (${v.width}x${v.height})`).join(', ')}`);

const allRules = new Set();

for (const vp of viewports) {
  console.error(`  Processing ${vp.name}...`);
  const { criticalRules } = await extractCriticalCss(url, vp);
  for (const rule of criticalRules) {
    allRules.add(rule);
  }
  console.error(`    Found ${criticalRules.length} rules`);
}

// Deduplicate and combine
let combined = [...allRules].join('\n');

// Basic minification
combined = combined
  .replace(/\/\*[\s\S]*?\*\//g, '')    // Remove comments
  .replace(/\s+/g, ' ')                // Collapse whitespace
  .replace(/\s*{\s*/g, '{')            // Remove space around {
  .replace(/\s*}\s*/g, '}')            // Remove space around }
  .replace(/\s*;\s*/g, ';')            // Remove space around ;
  .replace(/\s*:\s*/g, ':')            // Remove space around :
  .replace(/\s*,\s*/g, ',')            // Remove space around ,
  .trim();

console.error(`\nTotal combined rules: ${allRules.size}`);
console.error(`Output size: ${(combined.length / 1024).toFixed(1)} KB`);

if (outFile) {
  writeFileSync(outFile, combined, 'utf8');
  console.error(`Written to: ${outFile}`);
} else {
  process.stdout.write(combined);
}
