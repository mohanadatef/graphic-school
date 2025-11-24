/**
 * Check if Frontend server is running
 * Used before running Cypress tests
 */

import http from 'http';

const SERVER_URL = 'http://localhost:5173';
const TIMEOUT = 5000;

function checkServer() {
  return new Promise((resolve, reject) => {
    const req = http.get(SERVER_URL, { timeout: TIMEOUT }, (res) => {
      resolve(true);
    });

    req.on('error', (err) => {
      reject(err);
    });

    req.on('timeout', () => {
      req.destroy();
      reject(new Error('Server check timeout'));
    });
  });
}

async function main() {
  try {
    await checkServer();
    console.log('✅ Frontend server is running on http://localhost:5173');
    process.exit(0);
  } catch (error) {
    console.error('❌ Frontend server is NOT running!');
    console.error('');
    console.error('Please start the server first:');
    console.error('  cd graphic-school-frontend');
    console.error('  npm run dev');
    console.error('');
    console.error('Then run the tests in another terminal:');
    console.error('  npm run cypress:run');
    process.exit(1);
  }
}

main();

