@props(['tab' => 1])

<div class="mt-2">
    <span {{$attributes->merge(['class' => 'cursor-pointer text-blue-500 font-medium']) }} @click="selected !== {{$tab}} ? selected = {{$tab}} : selected = null">{{$slot}}</span>
</div>