<?php

namespace App\Console\Commands\Menu;

use App\Models\Menu\Menu;
use App\Services\Menu\MenuImageCreator;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;

class MenuImageCreateCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:MenuImageCreate';

    public function handle(): void
    {
        $ids = Menu::orderBy('order', 'asc')->pluck('id');

        $this->output->progressStart(count($ids));

        $menuImageCreator = new MenuImageCreator;

        foreach ($ids as $id) {
            try {
                $menuImageCreator->createMenuImage($id);
                $this->output->progressAdvance();
            } catch (\Exception $e) {
                $this->error("Failed to create image for menu ID {$id}: {$e->getMessage()}");
            }
        }

        $this->output->progressFinish();
        $this->clearCache('get-all-menus');
    }
}
