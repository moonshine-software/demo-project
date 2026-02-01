<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task;


use App\Models\Status;
use App\Models\Task;
use App\MoonShine\Resources\Task\Pages\TaskIndexPage;
use Illuminate\Support\Collection;
use Leeto\MoonShineKanBan\DTOs\KanbanItem;
use Leeto\MoonShineKanBan\Resources\KanBanResource;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

#[Icon('view-columns')]
#[Group('Kanban', 'squares-2x2')]
#[Order(10)]
final class TaskResource extends KanBanResource
{
    protected string $model = Task::class;

    protected string $title = 'Tasks';

    protected string $sortColumn = 'sorting';

    protected ?string $description = 'description';

    protected array $with = [
        'status',
        'user',
    ];

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    public function statuses(): Collection
    {
        return Status::query()
            ->orderBy('sorting')
            ->pluck('name', 'id');
    }

    public function foreignKey(): string
    {
        return 'status_id';
    }

    protected function formFields(): iterable
    {
        return [
            ID::make(),
            Select::make('Status', 'status_id')
                ->options($this->statuses()->toArray())
                ->required(),

            BelongsTo::make('User'),

            Text::make('Title')->required(),
            TinyMce::make('Description'),
        ];
    }

    protected function pages(): array
    {
        return [
            TaskIndexPage::class,
            FormPageContract::class,
        ];
    }

    public function getItems(): iterable
    {
        $items = new Collection;

        foreach (parent::getItems() as $task) {
            $item = KanbanItem::make(
                id: $task->id,
                title: $task->title,
                status: $this->foreignKey(),
            )
                ->setModel($task)
                ->setSubtitle(str($task->description)->limit(50)->value())
                //->setThumbnail(asset('images/template.jpg'))
                ->addLabel(fake()->word(), 'red')
                ->addLabel(fake()->word(), 'green')
                ->setUser(
                    avatar: asset('images/template.jpg'),
                    name: $task->user->name,
                )
                ->addMeta('user', $task->user->name)
                ->addMeta('chat-bubble-left', (string) random_int(0, 100))
                ->addMeta('users', (string) random_int(0, 50))
                ->setButtons([])
            ;

            $items->push($item);
        }

        return $items;
    }
}
