<div>
    @php
    $wizardHeadings = [
    '1' => 'Select Model',
    '2' => 'Select Features',
    '3' => 'Select Fields',
    '4' => 'Relations',
    '5' => 'Sort Fields',
    '6' => 'Advanced',
    '7' => 'Generate Files',
    ];
    @endphp


    <div class="h-32 grid grid-rows-1 grid-flow-col gap-0">
        @for ($i = 1; $i <= $totalSteps; $i++) <x-tall-crud-wizard-step :active="$i <= $step"
            :current="$i == $step" :isLast="$i == $totalSteps">
            {{$i}}
            <x-slot name="content">
                {{$wizardHeadings[$i]}}
            </x-slot>
            </x-tall-crud-wizard-step>
            @endfor
    </div>

    <x-tall-crud-h2 class="border-b-2 border-gray-300 text-2xl">{{$wizardHeadings[$step]}}
    </x-tall-crud-h2>

    <div class="border-b-2 border-gray-300 py-4 px-6">
        @include('tall-crud-generator::livewire.step'.$step)
    </div>

    <div class="flex justify-between mt-4">
        @if($step != 1)
        <x-tall-crud-button class="ml-4" wire:click="moveBack">Previous</x-tall-crud-button>
        @else
        &nbsp;
        @endif
        <x-tall-crud-button class="mr-4" wire:click="moveAhead">
            {{$step != $totalSteps ? 'Next' : 'Generate Files' }}</x-tall-crud-button>
    </div>
</div>