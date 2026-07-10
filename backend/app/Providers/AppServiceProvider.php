<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            \Illuminate\Support\Facades\Storage::extend('google', function($app, $config) {
                \Log::info('Registering Google Drive Disk', ['clientId' => $config['clientId'] ?? 'missing', 'folderId' => $config['folderId'] ?? 'missing']);
                
                $options = [
                    'useDisplayPaths' => true,
                    'sharedFolderId'  => $config['folderId'] ?? null,
                ];
                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, null, $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                $config['throw'] = true;
                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter, $config);
            });
        } catch(\Exception $e) {
            \Log::error('failed to register google disk: ' . $e->getMessage());
        }
    }
}
