<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Component\Process\Process;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    // ⭐ GUNAKAN DatabaseMigrations untuk auto-rollback
    use DatabaseMigrations;

    protected static $chromeProcess;

    #[BeforeClass]
    public static function prepare(): void
    {
        // Kill all existing ChromeDriver processes
        exec('taskkill /F /IM chromedriver.exe 2>nul');
        exec('taskkill /F /IM chromedriver-win.exe 2>nul');
        usleep(500000);

        static::startChromeDriver(['--port=9515']);
    }

    // ⭐ HAPUS setUp() yang lama, biarkan DatabaseMigrations handle
    // Atau jika perlu seed, gunakan cara yang lebih efisien:
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Hanya seed data yang benar-benar dibutuhkan per test
        // JANGAN migrate:fresh --seed di sini!
    }

    public static function startChromeDriver(array $arguments = []): void
    {
        $driver = realpath(__DIR__.'/../vendor/laravel/dusk/bin/chromedriver-win.exe');
        
        if (!$driver || !file_exists($driver)) {
            throw new \Exception('ChromeDriver not found. Run: php artisan dusk:chrome-driver');
        }

        if (!in_array('--port=9515', $arguments)) {
            $arguments[] = '--port=9515';
        }

        static::$chromeProcess = new Process(array_merge([$driver], $arguments));
        static::$chromeProcess->start();
        
        sleep(2);
        
        echo "\nChromeDriver started on port 9515\n";
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$chromeProcess) {
            static::$chromeProcess->stop();
            echo "\nChromeDriver stopped\n";
        }
        
        parent::tearDownAfterClass();
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--headless=new',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->reject(function ($item) {
                return $item === '--headless=new';
            });
        })->all());

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
}