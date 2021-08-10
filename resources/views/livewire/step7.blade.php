<div>
    <div>
        <x:tall-crud-generator::label>Name of your Livewire Component</x:tall-crud-generator::label>
        <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="componentName" required />
        @error('componentName') <x:tall-crud-generator::error-message>{{$message}}
        </x:tall-crud-generator::error-message>@enderror
    </div>

    @if($isComplete)
    <div class="flex items-center justify-end">
        @if ($exitCode == 0)
        <div>
            <div class="text-green-500 font-bold italic">
                Files Generated Successfully! <br />
                Use the Following code to render Livewire Component.
            </div>
            <div class="bg-black text-white text-2xl mt-2 p-4 rounded-md">{{$generatedCode}}</div>

        </div>
        @else
        <x:tall-crud-generator::error-message>Files Could not be Generated.</x:tall-crud-generator::error-message>
        @endif
    </div>
    @endif
</div>