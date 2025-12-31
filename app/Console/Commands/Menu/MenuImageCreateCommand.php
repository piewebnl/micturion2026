<?php

namespace App\Console\Commands\Menu;

use App\Models\Menu\Menu;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use App\Traits\QueryCache\QueryCache;
use App\Services\Menu\MenuImageCreator;

// php artisan command:MenuImageCreate
class MenuImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:MenuImageCreate';

    private string $channel = 'menu_create_images';

    private JsonResponse $response;

    private bool $flushCache;

    public function handle()
    {

        Logger::deleteChannel($this->channel);

        // Get all ids that need to be created
        $ids = Menu::orderBy('order', 'asc')->pluck('id');

        $this->output->progressStart(count($ids));

        foreach ($ids as $id) {
            $menuImageCreator = new MenuImageCreator;
            $menuImageCreator->createMenuImage($id);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        $this->clearCache('get-all-menus');

        Logger::echo($this->channel);
    }
}
