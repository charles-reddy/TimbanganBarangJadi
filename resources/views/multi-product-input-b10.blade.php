<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input B10 Multi Product') }}
        </h2>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @livewire('multi-product-input-b10')
            </div>
        </div>
    </div>
</x-app-layout>
