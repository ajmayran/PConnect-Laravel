@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600 rounded-md shadow-sm']) }}>
