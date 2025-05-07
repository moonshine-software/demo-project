<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Pages\Article\ArticleDetailPage;
use App\MoonShine\Pages\Article\ArticleFormPage;
use App\MoonShine\Pages\Article\ArticleIndexPage;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\CardsBuilder;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
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

    protected bool $columnSelection = true;

    protected bool $stickyButtons = true;

    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public array $with = [
        'author',
    ];

    public string $column = 'title';

    protected int $itemsPerPage = 26;

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

    public function isListView(): bool
    {
        return session()?->get('view') === null || session()?->get('view') === 'list';
    }

    private function perPageValues(): array
    {
        return [
            6 => 6,
            12 => 12,
            26 => 26,
        ];
    }

    protected function getItemsPerPage(): int
    {
        $default = $this->itemsPerPage;
        $value = (int)(session()?->get('perPage') ?? $default);

        if (! in_array($value, $this->perPageValues())) {
            return $default;
        }

        return $value;
    }

    public function changeListingComponentState(MoonShineRequest $request): MoonShineJsonResponse
    {
        if (in_array($request->input('state'), ['perPage', 'view'])) {
            session()?->put($request->input('state'), $request->input('value'));
            session()?->put($request->input('state'), $request->get('value'));
        }

        if ($request->input('state') === 'perPage') {
            return MoonShineJsonResponse::make()
                ->events([
                    AlpineJs::event(
                        JsEvent::TABLE_UPDATED,
                        $this->getListComponentName(),
                    ),

                    AlpineJs::event(
                        JsEvent::CARDS_UPDATED,
                        $this->getListComponentName(),
                    ),
                ]);
        }

        return MoonShineJsonResponse::make()->redirect($this->getIndexPageUrl());
    }

    public function getListEventName(?string $name = null, array $params = []): string
    {
        $name ??= $this->getListComponentName();

        return AlpineJs::event(
            $this->isListView()
                ? JsEvent::TABLE_UPDATED
                : JsEvent::CARDS_UPDATED,
            $name,
            $params,
        );
    }

    /**
     * @param  TableBuilder  $component
     *
     */
    public function modifyListComponent(ComponentContract $component): ComponentContract
    {
        if (! $this->isListView()) {
            $component = CardsBuilder::make()
                ->componentAttributes([
                    'style' => 'margin-top: 5px',
                ])
                ->thumbnail(
                    fn(Article $article)
                        => $article->thumbnail
                        ? Storage::disk('public')->url($article->thumbnail)
                        : asset('images/template.jpg'),
                )
                ->fields($component->getFields())
                ->name($this->getListComponentName())
                ->async()
                ->cast($this->getCaster())
                ->items($component->getOriginalItems())
                ->buttons($this->getIndexButtons());
        }

        return $component
            ->topLeft(function (): array {
                return [];
            })
            ->topRight(function (): array {
                return [
                    Div::make([
                        Select::make('Per page')
                            ->onChangeMethod('changeListingComponentState', ['state' => 'perPage'])
                            ->options($this->perPageValues())
                            ->withoutWrapper()
                            ->native()
                            ->setValue($this->getItemsPerPage()),
                    ])->customAttributes([
                        'style' => 'width: 70px;',
                    ]),

                    Div::make([
                        ActionButton::make('')
                            ->method('changeListingComponentState', ['state' => 'view', 'value' => 'list'])
                            ->icon('list-bullet')
                            ->withoutLoading()
                            ->primary($this->isListView()),

                        ActionButton::make('')
                            ->method('changeListingComponentState', ['state' => 'view', 'value' => 'cards'])
                            ->icon('rectangle-group')
                            ->withoutLoading()
                            ->primary(! $this->isListView()),
                    ]),
                ];
            });
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->withConfirm(
            method: HttpMethod::DELETE,
            formBuilder: fn(FormBuilder $form, Article $item)
                => $form
                ->async(
                    events: [
                        $this->isListView()
                            ?
                            AlpineJs::event(
                                JsEvent::TABLE_ROW_UPDATED,
                                $this->getListComponentNameWithRow($item->getKey()),
                                array_filter([
                                    'page' => request()->getScalar('page'),
                                    'sort' => request()->getScalar('sort'),
                                ]),
                            )
                            : $this->getListEventName(
                            $this->getListComponentName(),
                            array_filter([
                                'page' => request()->getScalar('page'),
                                'sort' => request()->getScalar('sort'),
                            ]),
                        ),
                    ],
                )
                ->submit(
                    button: ActionButton::make(__('moonshine::ui.confirm'))->error()->hotKeys(['shift', 'd'], true),
                ),
        );
    }

    /** @param  TableBuilder  $component */
    public function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component->vertical(
            title: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(2),
            value: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(10),
        );
    }

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Article with author',
                static fn(Builder $q) => $q->whereNotNull('author_id'),
            ),

            QueryTag::make(
                'Article without an author',
                static fn(Builder $q) => $q->whereNull('author_id'),
            )->icon('users'),
        ];
    }

    public function metrics(): array
    {
        return [
            ValueMetric::make('Articles')
                ->value(Article::query()->count())
                ->columnSpan(6),
            ValueMetric::make('Comments')
                ->value(Comment::query()->count())
                ->columnSpan(6),
        ];
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

    public function trAttributes(): Closure
    {
        return static function (?DataWrapperContract $data, int $row): array {
            if ($data?->getOriginal()->author?->moonshine_user_role_id !== 1) {
                return [
                    'class' => 'bgc-gray',
                ];
            }

            return [];
        };
    }

    /**
     * @param  Article  $item
     *
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:2'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
            'thumbnail' => ['image'],
        ];
    }

    protected function beforeCreating(mixed $item): Model
    {
        if (! auth()->user()->isSuperUser()) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    protected function beforeUpdating(mixed $item): Model
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

    protected function filters(): iterable
    {
        return [
            Text::make('Title'),

            BelongsTo::make('Author', resource: UserResource::class)
                ->nullable()
                ->canSee(fn() => auth()->user()->isSuperUser()),

            Slug::make('Slug'),

            BelongsToMany::make('Categories')
                ->selectMode(),

            Switcher::make('Active'),
        ];
    }

    public function indexButtons(): ListOf
    {
        $tableName = $this->getIndexPage()->getListComponentName();

        return new ListOf(ActionButtonContract::class, [
            ...parent::indexButtons()->toArray(),

            ActionButton::make('Active', route('moonshine.articles.mass-active', $this->getUriKey()))
                ->inModal(fn() => 'Active', fn(): string
                    => (string)FormBuilder::make(
                    route('moonshine.articles.mass-active', $this->getUriKey()),
                    fields: [
                        HiddenIds::make($tableName),
                        FlexibleRender::make('<div>' . __('moonshine::ui.confirm_message') . '</div>'),
                        Text::make('To confirm, write "yes"', 'confirm')
                            ->customAttributes(['placeholder' => 'Or no']),
                    ],
                )
                    ->async(events: [AlpineJs::event(JsEvent::TABLE_UPDATED, $tableName)])
                    ->submit(__('moonshine::ui.confirm'), ['class' => 'btn-secondary']))
                ->bulk()
            ,

            ActionButton::make(
                'Go to',
                static fn(Article $model) => route('articles.show', $model),
            )->icon('paper-clip'),
        ]);
    }
}
