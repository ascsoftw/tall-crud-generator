<div {{$attributes->merge(['class' => 'relative overflow-hidden transition-all max-h-0 duration-700', 'style' => '']) }}>
    <div class="p-4 border-2 border-gray-300">
        {{$slot}}
    </div>
</div>