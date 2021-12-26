<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckTenantIsExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:expire_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查租户是否已过期';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         // 小心这里，get 如果没有数据，runForEach就会变成全部
        Tenant::whereDate('expired_at', '<', Carbon::now())
            ->get()
            ->runForEach(function ($item) {
                $this->info(strtotime($item->expired_at));
                $this->info('---');
                $this->info(strtotime( Carbon::now()) );
                if(strtotime($item->expired_at) < strtotime( Carbon::now())){
                    $this->info("过期...");
                    $this->info($item->id);
                    $item->putDownForMaintenance(['message' => '账号状态已过期']);
                }else{
                    $this->info($item->id);
                    $this->info("通过...");
                    //$item->putDownForMaintenance(['message' => '账号状态已过期']);
                }
                
            });
    }
}
