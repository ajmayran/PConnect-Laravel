<?php

return [
    'providers' => [
        App\Providers\BroadcastServiceProvider::class,
        SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
        App\Providers\NotificationServiceProvider::class,
        App\Providers\MailServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        App\Providers\LowStockServiceProvider::class,
        
    ],
    
];
    