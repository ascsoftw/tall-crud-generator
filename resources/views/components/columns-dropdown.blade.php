<x:tall-crud-generator::dropdown {{ $attributes->merge(['class' => 'flex justify-items items-center mr-4 border border-rounded px-2 cursor-pointer']) }}>
    <x-slot name="trigger">
        Columns
    </x-slot>

    <x-slot name="content">
        <template x-for="c in columns">
            <div>
                <input type="checkbox" x-model="selectedColumns" :value="c" />
                <span x-text="c"></span>
            </div>
        </template>
    </x-slot>
</x:tall-crud-generator::dropdown>