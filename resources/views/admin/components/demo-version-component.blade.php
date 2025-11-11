<div>
    @if(config('app.demo_mode', false))
        <x-moonshine::alert type="success">
            @lang('demo.database')
        </x-moonshine::alert>
        <x-moonshine::layout.line-break />
    @endif
</div>
