<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Weather') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen text-white antialiased" style="background-color: #162032; background-image: linear-gradient(to bottom, #1e293b, #0f172a);">
        <div class="max-w-[390px] mx-auto px-4 py-8 overflow-hidden">

            {{-- Current Weather Header --}}
            <div class="text-center">
                <h1 class="text-2xl font-medium text-white">London</h1>
                <div class="text-7xl font-thin text-white my-1">8&deg;</div>
                <div class="flex items-center justify-center gap-2 text-base text-slate-200">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9" />
                        <path d="M16 14v6" />
                        <path d="M8 14v6" />
                        <path d="M12 16v6" />
                    </svg>
                    <span>Drizzle</span>
                </div>
                <div class="text-sm text-slate-300 mt-1">H:8&deg; L:5&deg;</div>
            </div>

            {{-- Hourly Forecast Card --}}
            <div class="bg-slate-800/60 backdrop-blur rounded-2xl p-4 mt-6">
                <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-slate-400 uppercase pb-3 border-b border-slate-700/60">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>Hourly Forecast</span>
                </div>
                <div class="flex overflow-x-auto gap-5 pt-3">
                    @php
                        $hours = [
                            ['label' => 'Now', 'temp' => '8°'],
                            ['label' => '1am', 'temp' => '8°'],
                            ['label' => '2am', 'temp' => '7°'],
                            ['label' => '3am', 'temp' => '7°'],
                            ['label' => '4am', 'temp' => '6°'],
                            ['label' => '5am', 'temp' => '6°'],
                            ['label' => '6am', 'temp' => '5°'],
                            ['label' => '7am', 'temp' => '5°'],
                            ['label' => '8am', 'temp' => '5°'],
                            ['label' => '9am', 'temp' => '5°'],
                            ['label' => '10am', 'temp' => '6°'],
                        ];
                    @endphp
                    @foreach ($hours as $hour)
                        <div class="flex flex-col items-center gap-2 shrink-0">
                            <span class="text-xs text-slate-300">{{ $hour['label'] }}</span>
                            <svg class="w-7 h-7 text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9" />
                                <path d="M16 14v6" />
                                <path d="M8 14v6" />
                                <path d="M12 16v6" />
                            </svg>
                            <span class="text-sm font-medium text-white">{{ $hour['temp'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 10-Day Forecast Card --}}
            <div class="bg-slate-800/60 backdrop-blur rounded-2xl p-4 mt-4">
                <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-slate-400 uppercase pb-2 border-b border-slate-700/60">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <span>10-Day Forecast</span>
                </div>
                <div>
                    @php
                        $days = [
                            ['day' => 'Today', 'low' => 5, 'high' => 8],
                            ['day' => 'Tue', 'low' => 6, 'high' => 13],
                            ['day' => 'Wed', 'low' => 9, 'high' => 12],
                            ['day' => 'Thu', 'low' => 6, 'high' => 9],
                            ['day' => 'Fri', 'low' => 7, 'high' => 10],
                            ['day' => 'Sat', 'low' => 6, 'high' => 13],
                            ['day' => 'Sun', 'low' => 5, 'high' => 12],
                            ['day' => 'Mon', 'low' => 2, 'high' => 7],
                            ['day' => 'Tue', 'low' => 0, 'high' => 4],
                            ['day' => 'Wed', 'low' => 1, 'high' => 5],
                        ];
                        $globalMin = 0;
                        $globalMax = 13;
                        $range = $globalMax - $globalMin;
                    @endphp
                    @foreach ($days as $index => $day)
                        @php
                            $leftPct = (($day['low'] - $globalMin) / $range) * 100;
                            $widthPct = (($day['high'] - $day['low']) / $range) * 100;
                        @endphp
                        <div class="flex items-center gap-3 py-2.5 {{ $index < count($days) - 1 ? 'border-b border-slate-700/50' : '' }}">
                            <span class="w-14 font-medium text-sm text-white">{{ $day['day'] }}</span>
                            <svg class="w-8 h-8 text-slate-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9" />
                                <path d="M16 14v6" />
                                <path d="M8 14v6" />
                                <path d="M12 16v6" />
                            </svg>
                            <span class="w-8 text-sm text-slate-400 text-right">{{ $day['low'] }}&deg;</span>
                            <div class="flex-1 h-1.5 bg-slate-700 rounded-full relative">
                                <div class="absolute h-1.5 rounded-full bg-gradient-to-r from-cyan-400 to-green-400" style="left: {{ $leftPct }}%; width: {{ $widthPct }}%;"></div>
                            </div>
                            <span class="w-8 text-sm text-right font-medium text-white">{{ $day['high'] }}&deg;</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </body>
</html>