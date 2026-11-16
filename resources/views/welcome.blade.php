<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Weather') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen text-white antialiased overflow-x-hidden" style="background-color: #162032; background-image: linear-gradient(to bottom, #1e293b, #0f172a);">
        <div class="max-w-[390px] mx-auto px-4 py-8 overflow-x-hidden">

            {{-- Current Weather Header --}}
            <div class="text-center">
                <h1 class="text-2xl font-medium text-white">London</h1>
                <div class="text-7xl font-thin text-white my-1">8&deg;</div>
                <div class="flex items-center justify-center gap-2 text-base text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 19v1.5M12 19v1.5M16 19v1.5" />
                    </svg>
                    <span>Drizzle</span>
                </div>
                <div class="text-sm text-slate-300 mt-1">H:8&deg; L:5&deg;</div>
            </div>

            {{-- Hourly Forecast Card --}}
            <div class="rounded-2xl bg-slate-800/60 backdrop-blur p-4 mt-6">
                <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-slate-400 uppercase pb-3 border-b border-slate-700/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Hourly Forecast</span>
                </div>
                <div class="flex overflow-x-auto gap-5 pt-3">
                    @php
                        $hours = [
                            ['time' => 'Now', 'temp' => '8°'],
                            ['time' => '1am', 'temp' => '8°'],
                            ['time' => '2am', 'temp' => '7°'],
                            ['time' => '3am', 'temp' => '7°'],
                            ['time' => '4am', 'temp' => '6°'],
                            ['time' => '5am', 'temp' => '6°'],
                            ['time' => '6am', 'temp' => '5°'],
                            ['time' => '7am', 'temp' => '5°'],
                            ['time' => '8am', 'temp' => '5°'],
                            ['time' => '9am', 'temp' => '5°'],
                            ['time' => '10am', 'temp' => '6°'],
                        ];
                    @endphp
                    @foreach ($hours as $hour)
                        <div class="flex flex-col items-center gap-2 shrink-0">
                            <span class="text-xs text-slate-300">{{ $hour['time'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 19v1.5M12 19v1.5M16 19v1.5" />
                            </svg>
                            <span class="text-sm font-medium text-white">{{ $hour['temp'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 10-Day Forecast Card --}}
            <div class="rounded-2xl bg-slate-800/60 backdrop-blur p-4 mt-4">
                <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-slate-400 uppercase pb-2 border-b border-slate-700/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <span>10-Day Forecast</span>
                </div>
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
                    $minTemp = 0;
                    $maxTemp = 13;
                    $range = $maxTemp - $minTemp;
                @endphp
                <div>
                    @foreach ($days as $index => $day)
                        @php
                            $leftPct = (($day['low'] - $minTemp) / $range) * 100;
                            $widthPct = (($day['high'] - $day['low']) / $range) * 100;
                        @endphp
                        <div class="flex items-center gap-3 py-2.5 {{ $index < count($days) - 1 ? 'border-b border-slate-700/50' : '' }}">
                            <span class="w-14 font-medium text-sm text-white">{{ $day['day'] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 19v1.5M12 19v1.5M16 19v1.5" />
                            </svg>
                            <span class="w-8 text-sm text-slate-400">{{ $day['low'] }}&deg;</span>
                            <div class="flex-1 h-1.5 bg-slate-700 rounded-full relative">
                                <div class="absolute h-1.5 rounded-full bg-gradient-to-r from-cyan-400 to-green-400" style="left: {{ $leftPct }}%; width: {{ $widthPct }}%;"></div>
                            </div>
                            <span class="w-8 text-right text-sm font-medium text-white">{{ $day['high'] }}&deg;</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </body>
</html>