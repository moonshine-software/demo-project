import { spawn, spawnSync } from 'node:child_process';
import { mkdirSync, mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const directory = mkdtempSync(join(tmpdir(), 'moonshine-e2e-'));
const env = {
    ...process.env,
    APP_ENV: 'e2e',
    APP_KEY: 'base64:LkTwza9SaKmdzblmj4tFwmrIYnAfjtG/P+ksZz0qm/o=',
    APP_DEBUG: 'false',
    APP_URL: 'http://127.0.0.1:8005',
    APP_CONFIG_CACHE: join(directory, 'config.php'),
    APP_ROUTES_CACHE: join(directory, 'routes.php'),
    LARAVEL_STORAGE_PATH: join(directory, 'storage'),
    DEMO_MODE: 'false',
    DB_URL: '',
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: join(directory, 'database.sqlite'),
    SESSION_DRIVER: 'file',
    SESSION_COOKIE: 'moonshine_e2e_session',
    SESSION_DOMAIN: 'null',
    SESSION_SECURE_COOKIE: 'false',
    CACHE_STORE: 'array',
    QUEUE_CONNECTION: 'sync',
    MAIL_MAILER: 'array',
    DEBUGBAR_ENABLED: 'false',
    BCRYPT_ROUNDS: '4',
    MOONSHINE_ROUTE_PREFIX: 'admin',
};

process.on('exit', () => rmSync(directory, { recursive: true, force: true }));
for (const path of ['app/public', 'app/private', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs']) {
    mkdirSync(join(env.LARAVEL_STORAGE_PATH, path), { recursive: true });
}
writeFileSync(env.DB_DATABASE, '');

const migration = spawnSync('php', [
    'artisan', 'migrate', '--force', '--seed', '--seeder=Tests\\Fixtures\\E2ESeeder',
], { env, stdio: 'inherit' });

if (migration.error) throw migration.error;
if (migration.status !== 0) process.exit(migration.status ?? 1);

const server = spawn('php', [
    'artisan', 'serve', '--host=127.0.0.1', '--port=8005', '--no-reload', '--tries=1',
], { env, stdio: 'inherit' });

for (const signal of ['SIGINT', 'SIGTERM']) {
    process.on(signal, () => server.kill(signal));
}
server.on('error', (error) => { throw error; });
server.on('exit', (code) => process.exit(code ?? 0));
