<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $app = $this->getAppInfo();
        $database = $this->getDatabaseInfo();
        $queue = $this->getQueueInfo();
        $storage = $this->getStorageInfo();
        $cache = $this->getCacheAndSessionInfo();
        $mail = $this->getMailInfo();
        $backup = $this->getBackupInfo();

        $alerts = $this->buildAlerts($app, $database, $mail);

        return view('admin.pages.system-status.index', compact(
            'app',
            'database',
            'queue',
            'storage',
            'cache',
            'mail',
            'backup',
            'alerts'
        ));
    }

    private function getAppInfo(): array
    {
        return [
            'name' => Config::get('app.name'),
            'env' => Config::get('app.env'),
            'debug' => (bool) Config::get('app.debug'),
            'url' => Config::get('app.url'),
            'timezone' => Config::get('app.timezone'),
            'locale' => Config::get('app.locale'),
            'php_version' => PHP_VERSION,
            'laravel_version' => App::version(),
        ];
    }

    private function getDatabaseInfo(): array
    {
        $defaultConnection = Config::get('database.default');
        $connectionConfig = Config::get("database.connections.{$defaultConnection}", []);

        $status = 'ok';
        $statusMessage = 'متصل';
        $latencyMs = null;

        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latencyMs = round((microtime(true) - $start) * 1000, 2);
        } catch (\Throwable $e) {
            $status = 'error';
            $statusMessage = 'فشل الاتصال: ' . $e->getMessage();
        }

        return [
            'status' => $status,
            'status_message' => $statusMessage,
            'connection' => $defaultConnection,
            'driver' => $connectionConfig['driver'] ?? null,
            'host' => $connectionConfig['host'] ?? null,
            'database' => $connectionConfig['database'] ?? null,
            'latency_ms' => $latencyMs,
        ];
    }

    private function getQueueInfo(): array
    {
        $default = Config::get('queue.default');
        $connection = Config::get("queue.connections.{$default}", []);

        $jobsTable = Config::get('queue.connections.database.table', 'jobs');
        $failedConfig = Config::get('queue.failed', []);

        $jobsCount = null;
        $failedCount = null;

        try {
            if (Schema::hasTable($jobsTable)) {
                $jobsCount = DB::table($jobsTable)->count();
            }
        } catch (\Throwable $e) {
            $jobsCount = null;
        }

        try {
            $failedTable = $failedConfig['table'] ?? 'failed_jobs';
            if ($failedTable && Schema::hasTable($failedTable)) {
                $failedCount = DB::table($failedTable)->count();
            }
        } catch (\Throwable $e) {
            $failedCount = null;
        }

        return [
            'default' => $default,
            'driver' => $connection['driver'] ?? null,
            'connection_name' => $connection['connection'] ?? null,
            'queue' => $connection['queue'] ?? null,
            'jobs_count' => $jobsCount,
            'failed_jobs_count' => $failedCount,
        ];
    }

    private function getStorageInfo(): array
    {
        $defaultDisk = Config::get('filesystems.default', 'public');

        $canWriteStorage = $this->canWritePath(storage_path());
        $canWriteCache = $this->canWritePath(base_path('bootstrap/cache'));

        $diskOk = true;
        try {
            $diskOk = Storage::disk($defaultDisk)->exists('/');
        } catch (\Throwable $e) {
            $diskOk = false;
        }

        return [
            'default_disk' => $defaultDisk,
            'disk_ok' => $diskOk,
            'can_write_storage' => $canWriteStorage,
            'can_write_cache' => $canWriteCache,
        ];
    }

    private function getCacheAndSessionInfo(): array
    {
        return [
            'cache_store' => Config::get('cache.default'),
            'session_driver' => Config::get('session.driver'),
            'session_lifetime' => Config::get('session.lifetime'),
        ];
    }

    private function getMailInfo(): array
    {
        $defaultMailer = Config::get('mail.default');
        $mailerConfig = Config::get("mail.mailers.{$defaultMailer}", []);

        $host = $mailerConfig['host'] ?? null;
        $username = $mailerConfig['username'] ?? null;

        $missingCredentials = !$host || !$username;

        return [
            'default' => $defaultMailer,
            'transport' => $mailerConfig['transport'] ?? null,
            'host' => $host,
            'port' => $mailerConfig['port'] ?? null,
            'username' => $username,
            'from' => Config::get('mail.from.address'),
            'missing_credentials' => $missingCredentials,
        ];
    }

    private function getBackupInfo(): array
    {
        $latest = null;
        $stats = [
            'total' => 0,
            'completed' => 0,
            'failed' => 0,
        ];

        try {
            if (Schema::hasTable('backups')) {
                $query = Backup::query();
                $stats['total'] = $query->count();
                $stats['completed'] = Backup::where('status', 'completed')->count();
                $stats['failed'] = Backup::where('status', 'failed')->count();
                $latest = Backup::orderByDesc('created_at')->first();
            }
        } catch (\Throwable $e) {
            // ignore – table might not exist yet
        }

        return [
            'stats' => $stats,
            'latest' => $latest,
        ];
    }

    private function canWritePath(string $path): bool
    {
        try {
            if (!is_dir($path) || !is_writable($path)) {
                return false;
            }

            $testFile = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.health_check_' . uniqid() . '.tmp';
            if (@file_put_contents($testFile, 'ok') === false) {
                return false;
            }
            @unlink($testFile);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function buildAlerts(array $app, array $database, array $mail): array
    {
        $alerts = [];

        if ($app['env'] === 'production' && $app['debug']) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'التطبيق في وضع debug بينما البيئة Production. يُفضّل إيقاف debug في الإنتاج.',
            ];
        }

        if ($database['status'] === 'error') {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'فشل الاتصال بقاعدة البيانات: ' . $database['status_message'],
            ];
        }

        if ($mail['missing_credentials'] && $mail['default'] !== 'log') {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'إعدادات البريد غير مكتملة (المضيف أو اسم المستخدم مفقود).',
            ];
        }

        return $alerts;
    }
}

