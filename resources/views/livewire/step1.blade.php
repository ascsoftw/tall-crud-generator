<div>
    <x-tall-crud-label>Full Path to Your Model (e.g. App\Models\Product)</x-tall-crud-label>
    <x-tall-crud-input class="block mt-1 w-1/4" type="text" wire:model.defer="modelPath" required
        disabled="{{$isValidModel}}" />
    @error('modelPath') <x-tall-crud-error-message>{{$message}}</x-tall-crud-error-message>
    @enderror
</div>