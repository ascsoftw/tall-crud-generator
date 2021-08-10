<div>
    <x:tall-crud-generator::label>Full Path to Your Model (e.g. App\Models\Product)</x:tall-crud-generator::label>
    <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="modelPath" required
        disabled="{{$isValidModel}}" />
    @error('modelPath') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message>
    @enderror
</div>