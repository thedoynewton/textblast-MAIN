<button type="{{ $type }}" 
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center px-4 py-2 text-white font-medium rounded-lg transition ' .
        ($color === 'red' ? 'bg-red-600 hover:bg-red-700' : '') .
        ($color === 'gray' ? 'bg-gray-400 hover:bg-gray-500' : '') .
        ($color === 'yellow' ? 'bg-yellow-500 hover:bg-yellow-500' : '') .
        ($color === 'green' ? 'bg-green-500 hover:bg-green-600' : '')
    ]) }}>
    {{ $slot }}
</button>
