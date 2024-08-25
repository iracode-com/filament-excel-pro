<?php

namespace IracodeCom\FilamentExcelPro\Commands;

use IracodeCom\FilamentExcelPro\Commands\Concerns;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use function Laravel\Prompts\confirm;

class MakeExcelProPublishCommand extends Command
{
    use Concerns\CanManipulateFiles;

    public $signature = 'excel-pro:publish';

    public $description = "Publish filament excel pro's Resource.";

    public function handle(Filesystem $filesystem): int
    {
        $baseResourcePath       = app_path((string) Str::of('Filament\\Resources')->replace('\\', '/'));
        $importableResourcePath = app_path((string) Str::of('Filament\\Resources\\ImportableResource.php')->replace('\\', '/'));

        if ($this->checkForCollision([$importableResourcePath])) {
            $confirmed = confirm('Importable Resource already exists. Overwrite?');
            if (! $confirmed) {
                return self::INVALID;
            }
        }

        $filesystem->ensureDirectoryExists($baseResourcePath);
        $filesystem->copyDirectory(__DIR__ . '/../Resources', $baseResourcePath);

        $currentNamespace = 'IracodeCom\\FilamentExcelPro\\Resources';
        $newNamespace     = 'App\\Filament\\Resources';

        $this->replaceInFile($importableResourcePath, $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath . '/ImportableResource/Pages/CreateImportable.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath . '/ImportableResource/Pages/EditImportable.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath . '/ImportableResource/Pages/ImportImportable.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath . '/ImportableResource/Pages/ListImportables.php', $currentNamespace, $newNamespace);

        $this->components->info("Importable's Resource have been published successfully!");

        return self::SUCCESS;
    }
}
