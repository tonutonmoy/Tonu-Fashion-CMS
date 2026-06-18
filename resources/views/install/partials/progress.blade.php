@php
    $steps = [
        1 => ['label' => 'Requirements', 'route' => 'install.requirements'],
        2 => ['label' => 'Database', 'route' => 'install.database'],
        3 => ['label' => 'Store', 'route' => 'install.store'],
        4 => ['label' => 'Admin', 'route' => 'install.admin'],
        5 => ['label' => 'Install', 'route' => 'install.run'],
    ];
    $percent = ($current / count($steps)) * 100;
@endphp
<div>
    <div class="flex items-center justify-between text-sm text-gray-300 mb-3">
        <span>Step {{ $current }} of {{ count($steps) }}</span>
        <span>{{ round($percent) }}% complete</span>
    </div>
    <div class="h-2 bg-white/10 rounded-full overflow-hidden mb-6">
        <div class="h-full bg-rose-500 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
    </div>
    <ol class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs sm:text-sm">
        @foreach($steps as $number => $step)
            <li class="rounded-lg px-3 py-2 text-center border {{ $number <= $current ? 'bg-rose-500/20 border-rose-400 text-white' : 'bg-white/5 border-white/10 text-gray-400' }}">
                <span class="block font-semibold">{{ $number }}. {{ $step['label'] }}</span>
            </li>
        @endforeach
    </ol>
</div>
