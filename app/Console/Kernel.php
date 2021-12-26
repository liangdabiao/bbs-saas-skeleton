<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 检查租户是否过期
        $schedule->command('tenants:expire_check')->everyMinute()->withoutOverlapping();

        // 一小时执行一次『活跃用户』数据生成的命令
        // php artisan tenants:run larabbs:calculate-active-user
        $schedule->command('tenants:run larabbs:calculate-active-user')->everyMinute()->withoutOverlapping();

        // 每日零时执行一次
        $schedule->command('tenants:run larabbs:sync-user-actived-at')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
