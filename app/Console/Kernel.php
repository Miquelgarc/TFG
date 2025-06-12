<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// Importa tu comando personalizado:
use App\Console\Commands\UpdateAffiliateLevels;

class Kernel extends ConsoleKernel
{
    /**
     * Registra los comandos Artisan.
     */
    protected $commands = [
        UpdateAffiliateLevels::class,
        GenerateWeeklyInvoicesJob::class,
        \App\Console\Commands\GenerateInvoices::class,

    ];

    /**
     * Define la programación de comandos.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar el cron job cada día a las 2:00 AM
        $schedule->command('affiliates:update-levels')->dailyAt('02:00');
    }

    /**
     * Registra los comandos para Artisan (autoload).
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
