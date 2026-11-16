<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Weather') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            html, body { background: #111c31; }
            body { min-width: 0; }
            .weather-cloud { filter: drop-shadow(0 12px 18px rgb(8 15 31 / 16%)); }
            .forecast-scroll { scrollbar-width: none; }
            .forecast-scroll::-webkit-scrollbar { display: none; }
        </style>
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[#111c31] text-white antialiased">
        <main class="mx-auto w-full max-w-[430px] overflow-hidden px-4 pb-8 pt-7 sm:max-w-[900px] sm:px-8 sm:pt-9">
            <section class="relative flex min-h-[430px] flex-col items-center text-center">
                <h1 class="relative z-10 text-[44px] font-light leading-none tracking-[-1.5px] sm:text-[58px]">London</h1>
                <div class="relative mt-7 flex h-[270px] w-full max-w-[340px] items-start justify-center">
                    <div class="absolute top-[45px] h-[138px] w-[218px] rounded-[76px] bg-[#657087]/75 weather-cloud"></div>
                    <div class="absolute left-[56px] top-[22px] h-[118px] w-[118px] rounded-full bg-[#657087]/75 weather-cloud"></div>
                    <div class="absolute right-[52px] top-[48px] h-[105px] w-[105px] rounded-full bg-[#657087]/75 weather-cloud"></div>
                    <div class="relative z-10 text-[116px] font-thin leading-[.9] tracking-[-8px]">8°</div>
                    <div class="absolute bottom-[54px] z-10 flex items-center gap-3 text-[34px] font-light text-slate-100"><span class="text-3xl">🌧</span><span>Drizzle</span></div>
                    <div class="absolute bottom-[8px] z-10 text-[24px] font-medium text-slate-200">H:8° L:5°</div>
                </div>
            </section>

            @php
                $hours = [
                    ['time' => 'Now', 'temp' => '8°', 'icon' => 'rain'], ['time' => '1am', 'temp' => '8°', 'icon' => 'rain'], ['time' => '2am', 'temp' => '7°', 'icon' => 'rain'], ['time' => '3am', 'temp' => '7°', 'icon' => 'moon'], ['time' => '4am', 'temp' => '6°', 'icon' => 'rain'], ['time' => '5am', 'temp' => '6°', 'icon' => 'moon'], ['time' => '6am', 'temp' => '5°', 'icon' => 'moon'], ['time' => '7am', 'temp' => '5°', 'icon' => 'moon'], ['time' => '8am', 'temp' => '5°', 'icon' => 'sun'], ['time' => '9am', 'temp' => '5°', 'icon' => 'partly'], ['time' => '10am', 'temp' => '6°', 'icon' => 'sun'],
                ];
                $days = [
                    ['day' => 'Today', 'low' => 5, 'high' => 8, 'icon' => 'rain'], ['day' => 'Tue', 'low' => 6, 'high' => 13, 'icon' => 'rain'], ['day' => 'Wed', 'low' => 9, 'high' => 12, 'icon' => 'rain'], ['day' => 'Thu', 'low' => 6, 'high' => 9, 'icon' => 'rain'], ['day' => 'Fri', 'low' => 7, 'high' => 10, 'icon' => 'rain'], ['day' => 'Sat', 'low' => 6, 'high' => 13, 'icon' => 'rain'], ['day' => 'Sun', 'low' => 5, 'high' => 12, 'icon' => 'rain'], ['day' => 'Mon', 'low' => 2, 'high' => 7, 'icon' => 'partly'], ['day' => 'Tue', 'low' => 0, 'high' => 4, 'icon' => 'partly'], ['day' => 'Wed', 'low' => 1, 'high' => 5, 'icon' => 'partly'],
                ];
                $minTemp = 0; $maxTemp = 13; $range = $maxTemp - $minTemp;
            @endphp

            <section class="rounded-[30px] border border-white/20 bg-[#354157]/80 p-4 shadow-[0_12px_30px_rgb(4_10_26/14%)] backdrop-blur sm:p-8">
                <div class="flex items-center gap-3 border-b border-white/15 pb-4 text-[17px] font-semibold uppercase tracking-[.8px] text-slate-300"><span class="text-[25px] leading-none">◷</span><span>Hourly Forecast</span></div>
                <div class="forecast-scroll flex gap-7 overflow-x-auto pt-6 pb-1 sm:justify-between sm:gap-4">
                    @foreach ($hours as $hour)
                        <div class="flex min-w-[43px] shrink-0 flex-col items-center gap-6">
                            <span class="text-[18px] text-slate-300">{{ $hour['time'] }}</span>
                            @if ($hour['icon'] === 'moon') <span class="text-[38px] leading-none text-[#79cbe5]">☾</span>@elseif ($hour['icon'] === 'sun') <span class="text-[39px] leading-none text-amber-400">☀</span>@elseif ($hour['icon'] === 'partly') <span class="text-[31px] leading-none">🌤</span>@else <span class="text-[30px] leading-none">🌧</span>@endif
                            <span class="text-[23px] font-medium text-white">{{ $hour['temp'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mt-8 rounded-[30px] border border-white/20 bg-[#354157]/80 p-4 shadow-[0_12px_30px_rgb(4_10_26/14%)] backdrop-blur sm:p-8">
                <div class="flex items-center gap-3 border-b border-white/15 pb-4 text-[17px] font-semibold uppercase tracking-[.8px] text-slate-300"><span class="text-[23px] leading-none">▣</span><span>10-Day Forecast</span></div>
                <div>
                    @foreach ($days as $day)
                        @php $leftPct = (($day['low'] - $minTemp) / $range) * 100; $widthPct = (($day['high'] - $day['low']) / $range) * 100; @endphp
                        <div class="grid grid-cols-[58px_34px_32px_minmax(0,1fr)_32px] items-center gap-2 border-b border-white/10 py-5 last:border-b-0 sm:grid-cols-[70px_40px_38px_minmax(0,1fr)_38px] sm:gap-3">
                            <span class="text-[20px] font-medium text-white sm:text-[24px]">{{ $day['day'] }}</span><span class="text-[23px]">{{ $day['icon'] === 'partly' ? '🌤' : '🌧' }}</span><span class="text-[19px] text-white">{{ $day['low'] }}°</span><div class="relative h-3 rounded-full bg-slate-500/70"><div class="absolute h-3 rounded-full bg-gradient-to-r from-cyan-400 via-emerald-400 to-lime-400" style="left: {{ $leftPct }}%; width: {{ $widthPct }}%;"></div></div><span class="text-right text-[19px] font-medium text-white">{{ $day['high'] }}°</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>
    </body>
</html>
