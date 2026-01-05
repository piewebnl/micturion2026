<?php

namespace App\Console\Commands\Menu;

use App\Models\Menu\Menu;
use App\Services\Logger\Logger;
use App\Services\Menu\MenuImageCreator;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

class MenuImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:MenuImageCreate';

    protected $description = 'Create menu images for the homepage';

    private string $channel = 'menu_create_images';

    public function handle(): void
    {

        Logger::deleteChannel($this->channel);
        Logger::echoChannel($this->channel, $this);

        $ids = Menu::orderBy('order', 'asc')->pluck('id');

        if ($ids->isEmpty()) {
            Logger::log('error', $this->channel, 'No Menu items found', [], $this);

            return;
        }

        $this->output->progressStart(count($ids));

        $clearCache = false;

        foreach ($ids as $id) {
            $MenuImageCreator = new MenuImageCreator;
            $status = $MenuImageCreator->createMenuImage($id);
            if ($status) {
                $clearCache = true;
            }
            $this->output->progressAdvance();
        }

        if ($clearCache) {
            $this->clearCache('menus', $this->channel, $this);
        }

        $this->output->progressFinish();
    }
}
