<div id="view-worldclock" class="view-section hidden h-full">
    <div class="flex flex-col h-full">
        <div class="text-center mb-6 shrink-0">
            <h3 class="text-2xl font-heading font-bold dark:text-white text-slate-800 flex items-center justify-center gap-2">
                <i data-lucide="globe" class="w-6 h-6 text-teal-400"></i> World Clock
            </h3>
            <p class="opacity-60 dark:text-white text-slate-600">Click a country to see current time.</p>
        </div>
        
        <div class="flex-1 glass-panel p-4 relative overflow-hidden flex flex-col rounded-2xl">
            <!-- Digital Clock Overlay -->
            <div class="absolute top-4 left-1/2 transform -translate-x-1/2 z-10 bg-black/60 backdrop-blur-md px-8 py-4 rounded-2xl border border-white/20 text-center shadow-2xl pointer-events-none">
                <div id="selectedCountry" class="text-xs font-bold uppercase tracking-widest text-teal-300 mb-1">SELECT A COUNTRY</div>
                <div id="digitalClock" class="text-5xl font-mono font-bold text-white tracking-widest">--:--:--</div>
                <div id="currentDate" class="text-sm text-white/70 mt-1">--</div>
            </div>

            <!-- Map Container -->
            <div id="world-map" class="w-full h-full" style="min-height: 400px; position: relative; z-index: 1;"></div>
        </div>
    </div>
</div>