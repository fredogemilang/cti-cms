<?php

namespace Themes\Csi;

use App\Models\CustomPostType;
use App\Models\Page;
use App\Support\Filter;
use Illuminate\Support\ServiceProvider;
use Themes\Csi\Support\CsiDataProvider;

class CsiThemeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Filter::add('home.view_data', function (array $data, Page $page): array {
            return array_merge($data, CsiDataProvider::homeViewData());
        });

        Filter::add('cpt_archive.per_page', function (int $perPage, CustomPostType $postType): int {
            return $postType->slug === 'technology-alliances' ? 100 : $perPage;
        });
    }
}
