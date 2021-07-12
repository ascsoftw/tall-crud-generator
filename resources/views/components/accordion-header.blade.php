@props(['tab' => 1])

<div {{$attributes->merge(['class' => 'cursor-pointer text-blue-500 font-medium mt-2']) }} @click="selected !== {{$tab}} ? selected = {{$tab}} : selected = null">
    {{$slot}}
</div>