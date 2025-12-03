import { defineConfig } from 'cypress';
import fs from 'fs';
import path from 'path';
import { promisify } from 'util';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const writeFile = promisify(fs.writeFile);
const mkdir = promisify(fs.mkdir);
const appendFile = promisify(fs.appendFile);

export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost:5173',
    viewportWidth: 1280,
    viewportHeight: 720,
    video: true,
    screenshotOnRunFailure: true,
    videosFolder: 'cypress/videos',
    screenshotsFolder: 'cypress/screenshots',
    defaultCommandTimeout: 15000,
    requestTimeout: 15000,
    responseTimeout: 15000,
    pageLoadTimeout: 30000,
    // Environment variables for error handling
    env: {
      // Set to 'true' to allow 404 errors (default: false - stops on 404)
      ALLOW_404: process.env.CYPRESS_ALLOW_404 || 'false',
      // 304 is a normal cache response, allowed by default
      // Set to 'false' to stop on 304 errors (default: true - allows 304)
      ALLOW_304: process.env.CYPRESS_ALLOW_304 !== 'false' ? 'true' : 'false',
      // Set to 'true' to allow 500 errors (default: false - stops on 500)
      ALLOW_500: process.env.CYPRESS_ALLOW_500 || 'false',
    },
    setupNodeEvents(on, config) {
      
      const LOGS_DIR = path.join(process.cwd(), 'cypress', 'e2e-logs');
      const SPEC_RESULTS_DIR = path.join(LOGS_DIR, 'spec-results');
      const SUMMARY_FILE = path.join(LOGS_DIR, 'summary.json');
      const I18N_MISSING_LOG = path.join(LOGS_DIR, 'i18n-missing.log');
      
      // Ensure directories exist
      function ensureDirs() {
        try {
          if (!fs.existsSync(LOGS_DIR)) {
            fs.mkdirSync(LOGS_DIR, { recursive: true });
          }
          if (!fs.existsSync(SPEC_RESULTS_DIR)) {
            fs.mkdirSync(SPEC_RESULTS_DIR, { recursive: true });
          }
        } catch (error) {
          console.warn('[E2E Logger] Failed to create directories:', error.message);
        }
      }
      
      ensureDirs();
      
      // Track run summary
      const runSummary = {
        startedAt: new Date().toISOString(),
        specs: [],
        totalSpecs: 0,
        passedSpecs: 0,
        failedSpecs: 0,
      };
      
      // After each spec
      on('after:spec', async (spec, results) => {
        try {
          ensureDirs();
          
          const specName = path.basename(spec.relative, path.extname(spec.relative));
          const specResultFile = path.join(SPEC_RESULTS_DIR, `${specName}.json`);
          
          const specResult = {
            specFile: spec.relative,
            startedAt: results.stats?.startedAt || new Date().toISOString(),
            endedAt: results.stats?.endedAt || new Date().toISOString(),
            status: results.stats?.failures > 0 ? 'failed' : 'passed',
            tests: results.tests?.map(test => ({
              title: test.title?.join(' > ') || '',
              fullTitle: test.fullTitle || '',
              state: test.state || 'pending',
              duration: test.duration || 0,
              error: test.err ? {
                message: test.err.message || '',
                stack: test.err.stack || '',
              } : null,
              screenshots: test.screenshots?.map(s => s.path) || [],
              video: results.video || null,
            })) || [],
          };
          
          await writeFile(specResultFile, JSON.stringify(specResult, null, 2), 'utf-8');
          
          // Update summary
          runSummary.specs.push(spec.relative);
          runSummary.totalSpecs++;
          if (specResult.status === 'passed') {
            runSummary.passedSpecs++;
          } else {
            runSummary.failedSpecs++;
          }
          
          console.log(`[E2E Logger] Logged spec result: ${specName}`);
        } catch (error) {
          console.warn('[E2E Logger] Failed to log spec result:', error.message);
        }
      });
      
      // After entire run
      on('after:run', async (results) => {
        try {
          ensureDirs();
          
          runSummary.endedAt = new Date().toISOString();
          
          await writeFile(SUMMARY_FILE, JSON.stringify(runSummary, null, 2), 'utf-8');
          
          console.log(`[E2E Logger] Run summary logged: ${runSummary.totalSpecs} specs, ${runSummary.passedSpecs} passed, ${runSummary.failedSpecs} failed`);
        } catch (error) {
          console.warn('[E2E Logger] Failed to log run summary:', error.message);
        }
      });
      
      // Task handlers
      on('task', {
        log(message) {
          console.log(message);
          return null;
        },
        // Self-healing tasks
        checkFileExists(filePath) {
          const fullPath = path.resolve(process.cwd(), filePath);
          return fs.existsSync(fullPath);
        },
        async generateE2ETest(routePath) {
          try {
            // Use createRequire for CommonJS modules
            const { createRequire } = await import('module');
            const require = createRequire(import.meta.url);
            const selfHealPath = path.join(process.cwd(), 'scripts', 'selfHealNode.js');
            const selfHeal = require(selfHealPath);
            const result = selfHeal.generateE2ETest(routePath);
            return { success: true, filePath: result };
          } catch (error) {
            console.warn('[Cypress] Failed to generate E2E test:', error.message);
            return { success: false, error: error.message };
          }
        },
        async generatePage(routePath) {
          try {
            // Use createRequire for CommonJS modules
            const { createRequire } = await import('module');
            const require = createRequire(import.meta.url);
            const selfHealPath = path.join(process.cwd(), 'scripts', 'selfHealNode.js');
            const selfHeal = require(selfHealPath);
            const result = selfHeal.handle404Route(routePath);
            return { success: true, result };
          } catch (error) {
            console.warn('[Cypress] Failed to generate page:', error.message);
            return { success: false, error: error.message };
          }
        },
        // Log i18n missing keys
        async logI18nMissing({ key, locale, spec, test }) {
          try {
            ensureDirs();
            const logEntry = {
              key,
              locale,
              spec,
              test,
              timestamp: new Date().toISOString(),
            };
            const line = JSON.stringify(logEntry) + '\n';
            await appendFile(I18N_MISSING_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log i18n missing:', error.message);
            return null;
          }
        },
        // Log visited route
        async logRoute({ route, status, spec, test }) {
          try {
            ensureDirs();
            const ROUTES_LOG = path.join(LOGS_DIR, 'routes.log');
            const logEntry = {
              route,
              status,
              source: 'e2e',
              timestamp: new Date().toISOString(),
              spec,
              test,
            };
            const line = JSON.stringify(logEntry) + '\n';
            await appendFile(ROUTES_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log route:', error.message);
            return null;
          }
        },
        // Report 404 errors
        async report404({ url, method, spec }) {
          try {
            ensureDirs();
            const ERRORS_LOG = path.join(LOGS_DIR, '404-errors.log');
            const logEntry = {
              type: '404',
              url,
              method,
              spec,
              timestamp: new Date().toISOString(),
            };
            const line = JSON.stringify(logEntry) + '\n';
            await appendFile(ERRORS_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log 404:', error.message);
            return null;
          }
        },
        // Report 500 errors
        async report500({ url, method, spec }) {
          try {
            ensureDirs();
            const ERRORS_LOG = path.join(LOGS_DIR, '500-errors.log');
            const logEntry = {
              type: '500',
              url,
              method,
              spec,
              timestamp: new Date().toISOString(),
            };
            const line = JSON.stringify(logEntry) + '\n';
            await appendFile(ERRORS_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log 500:', error.message);
            return null;
          }
        },
        // Report general errors
        async reportError(errorInfo) {
          try {
            ensureDirs();
            const ERRORS_LOG = path.join(LOGS_DIR, 'errors.log');
            const line = JSON.stringify(errorInfo) + '\n';
            await appendFile(ERRORS_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log error:', error.message);
            return null;
          }
        },
        // Report 404 pages
        async report404Page({ url, spec }) {
          try {
            ensureDirs();
            const ERRORS_LOG = path.join(LOGS_DIR, '404-pages.log');
            const logEntry = {
              type: '404-page',
              url,
              spec,
              timestamp: new Date().toISOString(),
            };
            const line = JSON.stringify(logEntry) + '\n';
            await appendFile(ERRORS_LOG, line, 'utf-8');
            return null;
          } catch (error) {
            console.warn('[E2E Logger] Failed to log 404 page:', error.message);
            return null;
          }
        },
      });
    },
    specPattern: 'cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'cypress/support/e2e.js',
    experimentalStudio: true,
    // Ignore uncaught exceptions from the app
    chromeWebSecurity: false,
  },
    reporter: "json",
    reporterOptions: {
        outputDir: "cypress/results",
        overwrite: true,
        mochaFile: "cypress/results/test-output.json"
    },
  component: {
    devServer: {
      framework: 'vue',
      bundler: 'vite',
    },
  },
});

