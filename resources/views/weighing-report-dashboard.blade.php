<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Timbangan') }}
        </h2>
    </x-slot>

    @livewire('weighing-report-dashboard')
</x-app-layout>
