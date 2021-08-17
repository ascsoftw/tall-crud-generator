<div>
    <ul>
        <li>
            <x:tall-crud-generator::button wire:click="showSortDialog('listing')">Listing </x:tall-crud-generator::button>
        </li>
        @if($this->addFeature)
        <li class="mt-4">
            <x:tall-crud-generator::button wire:click="showSortDialog('add')">Add Fields</x:tall-crud-generator::button>
        </li>
        @endif
        @if($this->editFeature)
        <li class="mt-4">
            <x:tall-crud-generator::button wire:click="showSortDialog('edit')">Edit Fields</x:tall-crud-generator::button>
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
