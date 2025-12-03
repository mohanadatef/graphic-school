/**
 * Generate E2E Test Report
 * Analyzes logs and generates a report of missing routes, pages, and APIs
 */

const fs = require('fs');
const path = require('path');

const LOGS_DIR = path.join(__dirname, '..', 'cypress', 'e2e-logs');
const REPORT_FILE = path.join(LOGS_DIR, 'e2e-report.json');

function generateReport() {
  const report = {
    generatedAt: new Date().toISOString(),
    routes: {
      visited: [],
      missing: [],
    },
    apis: {
      called: [],
      missing: [],
      errors: [],
    },
    pages: {
      visited: [],
      missing: [],
    },
    errors: {
      '404': [],
      '500': [],
    },
    i18n: {
      missing: [],
    },
  };

  // Read routes.log
  const routesLog = path.join(LOGS_DIR, 'routes.log');
  if (fs.existsSync(routesLog)) {
    const lines = fs.readFileSync(routesLog, 'utf-8').split('\n').filter(Boolean);
    lines.forEach((line) => {
      try {
        const entry = JSON.parse(line);
        if (entry.status === 200) {
          report.routes.visited.push(entry);
        } else if (entry.status === 404) {
          report.routes.missing.push(entry);
          report.pages.missing.push(entry);
        }
      } catch (e) {
        // Skip invalid JSON
      }
    });
  }

  // Read 404-errors.log
  const errors404Log = path.join(LOGS_DIR, '404-errors.log');
  if (fs.existsSync(errors404Log)) {
    const lines = fs.readFileSync(errors404Log, 'utf-8').split('\n').filter(Boolean);
    lines.forEach((line) => {
      try {
        const entry = JSON.parse(line);
        report.errors['404'].push(entry);
        if (entry.url.includes('/api/')) {
          report.apis.missing.push(entry);
        } else {
          report.routes.missing.push(entry);
        }
      } catch (e) {
        // Skip invalid JSON
      }
    });
  }

  // Read 500-errors.log
  const errors500Log = path.join(LOGS_DIR, '500-errors.log');
  if (fs.existsSync(errors500Log)) {
    const lines = fs.readFileSync(errors500Log, 'utf-8').split('\n').filter(Boolean);
    lines.forEach((line) => {
      try {
        const entry = JSON.parse(line);
        report.errors['500'].push(entry);
        report.apis.errors.push(entry);
      } catch (e) {
        // Skip invalid JSON
      }
    });
  }

  // Read 404-pages.log
  const pages404Log = path.join(LOGS_DIR, '404-pages.log');
  if (fs.existsSync(pages404Log)) {
    const lines = fs.readFileSync(pages404Log, 'utf-8').split('\n').filter(Boolean);
    lines.forEach((line) => {
      try {
        const entry = JSON.parse(line);
        report.pages.missing.push(entry);
      } catch (e) {
        // Skip invalid JSON
      }
    });
  }

  // Read i18n-missing.log
  const i18nLog = path.join(LOGS_DIR, 'i18n-missing.log');
  if (fs.existsSync(i18nLog)) {
    const lines = fs.readFileSync(i18nLog, 'utf-8').split('\n').filter(Boolean);
    lines.forEach((line) => {
      try {
        const entry = JSON.parse(line);
        report.i18n.missing.push(entry);
      } catch (e) {
        // Skip invalid JSON
      }
    });
  }

  // Deduplicate arrays
  report.routes.visited = deduplicate(report.routes.visited, 'route');
  report.routes.missing = deduplicate(report.routes.missing, 'url');
  report.apis.missing = deduplicate(report.apis.missing, 'url');
  report.apis.errors = deduplicate(report.apis.errors, 'url');
  report.pages.missing = deduplicate(report.pages.missing, 'url');
  report.errors['404'] = deduplicate(report.errors['404'], 'url');
  report.errors['500'] = deduplicate(report.errors['500'], 'url');
  report.i18n.missing = deduplicate(report.i18n.missing, 'key');

  // Write report
  if (!fs.existsSync(LOGS_DIR)) {
    fs.mkdirSync(LOGS_DIR, { recursive: true });
  }
  fs.writeFileSync(REPORT_FILE, JSON.stringify(report, null, 2));

  // Generate summary
  console.log('\n=== E2E Test Report ===\n');
  console.log(`Generated: ${report.generatedAt}\n`);
  console.log(`Routes Visited: ${report.routes.visited.length}`);
  console.log(`Routes Missing: ${report.routes.missing.length}`);
  console.log(`APIs Missing: ${report.apis.missing.length}`);
  console.log(`API Errors (500): ${report.apis.errors.length}`);
  console.log(`Pages Missing: ${report.pages.missing.length}`);
  console.log(`404 Errors: ${report.errors['404'].length}`);
  console.log(`500 Errors: ${report.errors['500'].length}`);
  console.log(`Missing i18n Keys: ${report.i18n.missing.length}\n`);

  if (report.routes.missing.length > 0) {
    console.log('Missing Routes:');
    report.routes.missing.forEach((r) => console.log(`  - ${r.url || r.route} (${r.method || 'GET'})`));
    console.log('');
  }

  if (report.apis.missing.length > 0) {
    console.log('Missing APIs:');
    report.apis.missing.forEach((a) => console.log(`  - ${a.url} (${a.method})`));
    console.log('');
  }

  if (report.apis.errors.length > 0) {
    console.log('API Errors (500):');
    report.apis.errors.forEach((a) => console.log(`  - ${a.url} (${a.method})`));
    console.log('');
  }

  if (report.pages.missing.length > 0) {
    console.log('Missing Pages:');
    report.pages.missing.forEach((p) => console.log(`  - ${p.url || p.route}`));
    console.log('');
  }

  console.log(`\nFull report saved to: ${REPORT_FILE}\n`);

  return report;
}

function deduplicate(array, key) {
  const seen = new Set();
  return array.filter((item) => {
    const value = item[key];
    if (seen.has(value)) {
      return false;
    }
    seen.add(value);
    return true;
  });
}

// Run if called directly
if (require.main === module) {
  generateReport();
}

module.exports = { generateReport };

