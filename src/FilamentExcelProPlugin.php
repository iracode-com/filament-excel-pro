<?php

namespace IracodeCom\FilamentExcelPro;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentExcelProPlugin implements Plugin
{
    use EvaluatesClosures;

    protected ?string             $resource             = null;
    protected string|Closure|null $label                = null;
    protected string|Closure|null $navigationGroup      = null;
    protected ?string             $navigationIcon       = null;
    protected ?int                $navigationSort       = null;
    protected ?bool               $navigationCountBadge = null;
    protected string|Closure|null $pluralLabel          = null;
    protected bool|Closure        $authorizeUsing       = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'iracode-com/filament-excel-pro';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            $this->resource ?? config('filament-excel-pro.resources.resource')
        ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getResource(): string
    {
        return $this->resource ?? config('filament-excel-pro.resources.resource');
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? config('filament-excel-pro.resources.label');
    }

    public function getPluralLabel(): string
    {
        return $this->evaluate($this->pluralLabel) ?? config('filament-excel-pro.resources.plural_label');
    }

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup) ?? config('filament-excel-pro.resources.navigation_group');
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon ?? config('filament-excel-pro.resources.navigation_icon');
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? config('filament-excel-pro.resources.navigation_sort');
    }

    public function getNavigationCountBadge(): ?bool
    {
        return $this->navigationCountBadge ?? config('filament-excel-pro.resources.navigation_count_badge');
    }

    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function pluralLabel(string|Closure $label): static
    {
        $this->pluralLabel = $label;

        return $this;
    }

    public function navigationGroup(string|Closure|null $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function navigationCountBadge(bool $show = true): static
    {
        $this->navigationCountBadge = $show;

        return $this;
    }

    public function authorize(bool|Closure $callback = true): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorizeUsing) === true;
    }
}