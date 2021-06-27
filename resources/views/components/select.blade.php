<select {!! $attributes->merge(['class' => 'focus:ring focus:ring-opacity-50 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring-indigo-200']) !!}
>
    {{$slot}}
</select>

