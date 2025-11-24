/**
 * Route Logger for E2E Testing
 * Logs all visited routes with their status codes
 * 
 * Note: This runs in Node.js context (Cypress tasks), not browser
 */

// This file is used by Cypress tasks, so we need CommonJS
const fs = require('fs');
const path = require('path');

const LOGS_DIR = path.join(process.cwd(), 'cypress', 'e2e-logs');
const ROUTES_LOG = path.join(LOGS_DIR, 'routes.log');

// Ensure logs directory exists
function ensureLogsDir() {
  try {
    if (!fs.existsSync(LOGS_DIR)) {
      fs.mkdirSync(LOGS_DIR, { recursive: true });
    }
  } catch (error) {
    console.warn('[routeLogger] Failed to create logs directory:', error.message);
  }
}

/**
 * Log a visited route
 * @param {string} route - The route path
 * @param {number} status - HTTP status code (200, 404, etc.)
 * @param {object} extra - Additional metadata
 */
function logVisitedRoute(route, status, extra = {}) {
  try {
    ensureLogsDir();
    
    const logEntry = {
      route,
      status,
      source: 'e2e',
      timestamp: new Date().toISOString(),
      ...extra,
    };
    
    // Append as JSONL (one JSON object per line)
    const line = JSON.stringify(logEntry) + '\n';
    fs.appendFileSync(ROUTES_LOG, line, 'utf-8');
    
    console.log(`[routeLogger] Logged route: ${route} (${status})`);
  } catch (error) {
    // Don't break tests if logging fails
    console.warn('[routeLogger] Failed to log route:', error.message);
  }
}

/**
 * Clear routes log (useful for test cleanup)
 */
function clearRoutesLog() {
  try {
    if (fs.existsSync(ROUTES_LOG)) {
      fs.unlinkSync(ROUTES_LOG);
    }
  } catch (error) {
    console.warn('[routeLogger] Failed to clear routes log:', error.message);
  }
}

module.exports = {
  logVisitedRoute,
  clearRoutesLog,
};

