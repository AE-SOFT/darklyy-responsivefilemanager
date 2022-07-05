<?php
namespace Darkeum\ResponsiveFileManager;

use Boot\System\Console\Command;
use Boot\System\Encryption\Encrypter;
use Boot\System\Console\ConfirmableTrait;


class RFMGenerate extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfm:generate
                    {--show : Отображать ключ вместо изменения файлов}
                    {--force : Принудительное выполнение операции в рабочей среде}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установка приватного ключа Responsive File Manager';
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->generateRandomKey();
        if ($this->option('show')) {
            return $this->line('<comment>' . $key . '</comment>');
        }
        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }
        $this->laravel['config']['responsivefilemanager.access_keys'] = [$key];
        $this->info("Ключ RFM [$key] записан успешно.");
    }
    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        $this->info('Идет генерация ключа RFM..');
        return hash(
            'sha256',
            Encrypter::generateKey($this->laravel['config']['app.cipher'])
        );
    }
    /**
     * Set the application key in the environment file.
     *
     * @param string $key random sha256 hashed string
     *
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey =   isset($this->laravel['config']['responsivefilemanager.access_keys'][0]) ?
                        $this->laravel['config']['responsivefilemanager.access_keys'][0] : '';
        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }
        return $this->writeNewEnvironmentFileWith($key);
    }
    /**
     * Write a new environment file with the given key.
     *
     * @param string $key random sha256 hashed string
     *
     * @return bool
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        $file = file_get_contents($this->laravel->environmentFilePath());
        $o = preg_match('/RFM_KEY=/', $file);
        switch ($o) {
            case 1:
                $this->info('Идет перезапись ключа RFM..');
                return file_put_contents(
                    $this->laravel->environmentFilePath(),
                    preg_replace(
                        $this->keyReplacementPattern(),
                        'RFM_KEY=' . $key,
                        file_get_contents($this->laravel->environmentFilePath())
                    )
                );
            case 0:
                $this->info('Идет запись ключа RFM..');
                return file_put_contents(
                    $this->laravel->environmentFilePath(),
                    PHP_EOL . 'RFM_KEY=' . $key . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );
            default:
                $this->error('Ошибка чтения .env файла');
                return false;
        }
    }
    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $k =    isset($this->laravel['config']['responsivefilemanager.access_keys'][0]) ?
                $this->laravel['config']['responsivefilemanager.access_keys'][0] : '';
        $escaped = preg_quote('=' . $k, '/');
        return "/^RFM_KEY{$escaped}/m";
    }
}
