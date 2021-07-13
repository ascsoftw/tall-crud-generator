@props(['active' => false, 'current' => false, 'isLast' => false])

<div class="@if($active) bg-blue-500 text-black @else bg-gray-300 text-white @endif w-32">

    <div class="font-extrabold flex justify-center items-center">
        <div class="@if($current) bg-blue-700 text-white @else bg-white text-gray-700 @endif w-20 h-20 rounded-full inline-flex items-center justify-center font-bold mt-4 text-2xl font-extrabold">
            {{ $slot }}
        </div>
    </div>

    <div class="@if($current) text-white @else text-black @endif text-center">
        {{$content}}
    </div>

</div>

@if(!$isLast)
<div class="ml-6 h-16 w-5 mt-8">
    <svg class="h-full w-full text-gray-400" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
        <path d="M0 -2L20 40L0 82" vector-effect="non-scaling-stroke" stroke="currentcolor" stroke-linejoin="round"></path>
    </svg>
</div>
@endif