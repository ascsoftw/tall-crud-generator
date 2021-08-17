<div>
    <ul>
        <li class="flex">
            <span class="cursor-pointer text-blue-500 font-medium" wire:click="showSortDialog('listing')">Listing</span>
            <x:tall-crud-generator::tooltip>
                Change the Order of Columns displayed in the Listing
            </x:tall-crud-generator::tooltip>
        </li>
        @if($this->addFeature)
        <li class="flex mt-4">
            <span class="cursor-pointer text-blue-500 font-medium" wire:click="showSortDialog('add')">Add Fields</span>
            <x:tall-crud-generator::tooltip>
                Change the Order of Fields displayed in the Add Form
            </x:tall-crud-generator::tooltip>
        </li>
        @endif
        @if($this->editFeature)
        <li class="flex mt-4">
            <span class="cursor-pointer text-blue-500 font-medium" wire:click="showSortDialog('edit')">Edit Fields</span>
            <x:tall-crud-generator::tooltip>
                Change the Order of Fields displayed in the Edit Form
            </x:tall-crud-generator::tooltip>
        </li>
        @endif
    </ul>
</div>

<x:tall-crud-generator::dialog-modal wire:model="confirmingSorting">
    <x-slot name="title">
        Sort Fields
    </x-slot>

    <x-slot name="content">
        <ul drag-root class="overflow-hidden rounded shadow divide-y">
            @if(!empty($this->sortingMode))
            @foreach($this->sortFields[$this->sortingMode] as $t)
                <li drag-item="{{ (isset($t['type']) && $t['type'] == 'withCount') ?  $t['field'] . ' (Count)' : $t['field']}}" draggable="true" wire:key="{{$t['field']}}" class="w-64 p-4 bg-white border">
                    {{$t['field']}}
                    {{ (isset($t['type']) && $t['type'] == 'withCount') ? '(Count)' : ''}}
                </li>
            @endforeach
            @endif
        </ul>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button mode="add" wire:click="hideSortDialog()">Done</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>

<script>
    window.addEventListener('init-sort-events', event => {
        let root = document.querySelector('[drag-root]')
        root.querySelectorAll('[drag-item]').forEach( el => {
            el.addEventListener('dragstart' , e => {
                e.target.setAttribute('dragging', true);
            })

            el.addEventListener('drop' , e => {
                e.target.classList.remove('bg-yellow-200')
                let draggingEl = root.querySelector('[dragging]')
                e.target.before(draggingEl);
                let component = window.Livewire.find(
                    e.target.closest('[wire\\:id]').getAttribute('wire:id')
                )

                let orderIds = Array.from(root.querySelectorAll('[drag-item]'))
                    .map(itemEl => itemEl.getAttribute('drag-item'))
                component.call('reorder', orderIds);
            })
            
            el.addEventListener('dragenter' , e => {
                e.target.classList.add('bg-yellow-200')
                e.preventDefault();
            })
            el.addEventListener('dragover' , e => e.preventDefault())
            el.addEventListener('dragleave' , e => {
                e.target.classList.remove('bg-yellow-200')
            })

            el.addEventListener('dragend' , e => {
                e.target.removeAttribute('dragging');
            })
        })
    })
</script>
