<?php

namespace Laravel\Passport\Console;

use Carbon\Carbon;
use Laravel\Passport\Token;
use Laravel\Passport\AuthCode;
use Illuminate\Console\Command;
use Laravel\Passport\RefreshToken;

class FlushCommand extends Command
{
    private $models = [
        AuthCode::class,
        RefreshToken::class,
        Token::class,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:flush {--force : Delete expired/revoked entries without the prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all expired and revoked Passport records';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->models as $model) {
            $this->findExpired($model);
        }
    }

    /*
     * Should be in an expired scope on the model, instead of this method.
     * This method takes a class name and checks to see if any results are expired.
     * If the results are expired it will prompt for deletion.
     *
     * @param string $modelName
     */
    private function findExpired($modelName)
    {
        if ($expired_count = $this->countExpired($modelName)) {
            $this->info("Found approx. {$expired_count} expired or revoked {$modelName}s.");

            if ($this->option('force') || $this->confirm("Do you wish to delete the expired/revoked items?")) {
                $this->deleteExpired($modelName);
            }
        } else {
            $this->info("No expired {$modelName} found.");
        }
    }

    private function expiredOrRevoked($modelName) {
        $model = new $modelName;
        return $model->where('expires_at', '<', Carbon::now())->orWhere('revoked', true);
    }

    private function countExpired($modelName)
    {
        return $this->expiredOrRevoked($modelName)->count();
    }

    private function deleteExpired($modelName)
    {
        return $this->expiredOrRevoked($modelName)->delete();
    }
}
