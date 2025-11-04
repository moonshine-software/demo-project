<?php

namespace App\MoonShine\Resources\Article;

use App\Models\Article;
use App\MoonShine\Resources\Article\Pages\ArticleDetailPage;
use App\MoonShine\Resources\Article\Pages\ArticleFormPage;
use App\MoonShine\Resources\Article\Pages\ArticleIndexPage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Group('Blog', 'newspaper')]
#[Icon('newspaper')]
#[Order(3)]
class ArticleResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    public string $model = Article::class;

    public string $title = 'Articles';

    public string $sortColumn = 'created_at';

    public bool $withPolicy = true;

    public array $with = ['author'];

    public string $column = 'title';

    protected int $itemsPerPage = 26;

    public function getItemsPerPage(): int
    {
        $default = $this->itemsPerPage;
        $value = (int)(session()?->get('perPage') ?? $default);

        if (! in_array($value, $this->perPageValues())) {
            return $default;
        }

        return $value;
    }

    public function perPageValues(): array
    {
        return [
            6 => 6,
            12 => 12,
            26 => 26,
        ];
    }

    protected function pages(): array
    {
        return [
            ArticleIndexPage::class,
            ArticleFormPage::class,
            ArticleDetailPage::class,
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Slug::make('Slug'),
        ];
    }

    protected function importFields(): iterable
    {
        return $this->exportFields();
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder
            ->withCount('comments')
            ->when(
                ! auth()->user()->isSuperUser(),
                fn($q) => $q->where('author_id', auth()->id()),
            );
    }

    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        if (! auth()->user()->isSuperUser()) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    protected function beforeUpdating(DataWrapperContract $item): DataWrapperContract
    {
        if (! auth()->user()->isSuperUser()) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    protected function search(): array
    {
        return ['id', 'title'];
    }
}
