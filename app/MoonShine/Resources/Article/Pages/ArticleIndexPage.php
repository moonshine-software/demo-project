<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Resources\Article\ArticleResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\User\UserResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ListRowEventType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\CardsBuilder;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends IndexPage<ArticleResource>
 */
final class ArticleIndexPage extends IndexPage
{
    /**
     * @return bool
     */
    public function isLazy(): bool
    {
        return $this->isListView();
    }

    protected function fields(): iterable
    {
        return array_filter([
            ID::make()->sortable(),

            BelongsTo::make('Author', resource: MoonShineUserResource::class),

            Number::make('Comments', 'comments_count'),

            Text::make('Title'),

            $this->isListView()
                ? Image::make('Thumbnail')->disk('public')->dir('articles')
                : null,

            RangeSlider::make('Age')->fromTo('age_from', 'age_to'),

            Number::make('Rating')
                ->stars(),

            Url::make('Link')
                ->link('https://cutcode.dev')
                ->blank(),

            Color::make('Color'),

            Switcher::make('Active')
                ->sortable(),
        ]);
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

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
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

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
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

    public function isListView(): bool
    {
        return session()?->get('view') === null || session()?->get('view') === 'list';
    }

    protected function buttons(): ListOf
    {
        $tableName = $this->getListComponentName();

        return parent::buttons()
            ->add(
                ActionButton::make('Active')
                    ->inModal(
                        'Active',
                        fn(): string => (string)FormBuilder::make(
                            route('moonshine.articles.mass-active', $this->getUriKey()),
                            fields: [
                                HiddenIds::make($tableName),
                                FlexibleRender::make('<div>' . __('moonshine::ui.confirm_message') . '</div>'),
                                Text::make('To confirm, write "yes"', 'confirm')
                                    ->customAttributes(['placeholder' => 'Or no']),
                            ],
                        )
                            ->async(events: [AlpineJs::event(JsEvent::TABLE_UPDATED, $tableName)])
                            ->submit(__('moonshine::ui.confirm'), ['class' => 'btn-secondary'])
                    )
                    ->bulk(),
            )
            ->add(
                ActionButton::make(
                    'Go to',
                    static fn(Article $model) => route('articles.show', $model),
                )->blank()->icon('paper-clip'),
            );
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button->withConfirm(
            method: HttpMethod::DELETE,
            formBuilder: fn(FormBuilder $form, Article $item) => $form->async(
                events: [
                    $this->isListView()
                        ?
                        AlpineJs::event(
                            JsEvent::TABLE_ROW_UPDATED,
                            $this->getResource()->getListComponentName(),
                            array_filter([
                                'key' => $item->getKey(),
                                'type' => ListRowEventType::REMOVE,
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
     * @param TableBuilder $component
     *
     */
    public function modifyListComponent(ComponentContract $component): ComponentContract
    {
        if (!$this->isListView()) {
            $component = CardsBuilder::make()
                ->componentAttributes([
                    'style' => 'margin-top: 5px',
                ])
                ->thumbnail(
                    fn(Article $article) => $article->thumbnail
                        ? Storage::disk('public')->url($article->thumbnail)
                        : asset('images/template.jpg'),
                )
                ->fields($component->getFields())
                ->name($this->getListComponentName())
                ->async()
                ->cast($this->getResource()->getCaster())
                ->buttons($this->getButtons())
                ->items($component->getOriginalItems());
        } else {
            $component
                ->trAttributes(static function (?DataWrapperContract $data, int $row): array {
                    if ($data?->getOriginal()->author?->moonshine_user_role_id !== 1) {
                        return [
                            'class' => 'bgc-gray',
                        ];
                    }

                    return [];
                })
                //->clickAction(ClickAction::EDIT)
                ->sticky()
                ->stickyButtons()
                ->columnSelection();
        }

        return $component
            ->topRight(function (): array {
                return [
                    Div::make([
                        Select::make('Per page')
                            ->onChangeMethod('changeListingComponentState', ['state' => 'perPage'])
                            ->options($this->getResource()->perPageValues())
                            ->withoutWrapper()
                            ->native()
                            ->setValue($this->getResource()->getItemsPerPage()),
                    ]),

                    Div::make([
                        ActionButton::make()
                            ->method('changeListingComponentState', ['state' => 'view', 'value' => 'list'])
                            ->icon('list-bullet')
                            ->withoutLoading()
                            ->primary($this->isListView()),

                        ActionButton::make()
                            ->method('changeListingComponentState', ['state' => 'view', 'value' => 'cards'])
                            ->icon('rectangle-group')
                            ->withoutLoading()
                            ->primary(!$this->isListView()),
                    ]),
                ];
            });
    }

    #[AsyncMethod]
    public function changeListingComponentState(CrudRequestContract $request): JsonResponse
    {
        if (in_array($request->input('state'), ['perPage', 'view'])) {
            session()?->put($request->input('state'), $request->input('value'));
            session()?->put($request->input('state'), $request->get('value'));
        }

        if ($request->input('state') === 'perPage') {
            return JsonResponse::make()
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

        return JsonResponse::make()->redirect($this->getResource()->getIndexPageUrl());
    }
}
