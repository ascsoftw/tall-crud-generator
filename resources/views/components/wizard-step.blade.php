@props(['active' => false, 'current' => false])

<div class="@if($active) bg-blue-500 text-black @else bg-gray-300 text-white @endif">
    <div class="font-extrabold flex justify-center items-center">
        <div class="@if($current) bg-blue-700 text-white @else bg-white text-gray-700 @endif w-20 h-20 rounded-full inline-flex items-center justify-center font-bold mt-4 text-2xl font-extrabold">
            {{ $slot }}
        </div>
    </div>

    <div class="@if($current) text-white @else text-black @endif text-center">
        {{$content}}
    </div>
</div>
