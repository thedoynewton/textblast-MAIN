<button type="{{ $type }}" 
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center px-4 py-2 text-white font-medium rounded-lg transition ' .
        ($color === 'red' ? 'bg-[#9d1e18] hover:bg-yellow-500' : '') .
        ($color === 'gray' ? 'bg-[#4B4B4B] hover:bg-[#606060]' : '') .
        ($color === 'yellow' ? 'bg-yellow-500 hover:bg-yellow-600' : '') .
        ($color === 'green' ? 'bg-green-500 hover:bg-green-600' : '')
    ]) }}>
    {{ $slot }}
</button>