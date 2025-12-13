<div id="view-speed" class="view-section hidden">
    <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-indigo-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-indigo-500/30">
                <i data-lucide="activity" class="w-10 h-10 text-indigo-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">Internet Speed Test</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Check your internet latency and estimated download speed.</p>
        </div>

        <div class="glass-panel p-8 text-center border-indigo-500/20 bg-indigo-500/5">
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <span class="text-xs uppercase tracking-widest opacity-60 dark:text-white text-slate-600">Ping / Latency</span>
                    <div class="text-4xl font-bold font-heading mt-2 text-indigo-400" id="pingResult">--</div>
                    <span class="text-xs opacity-50 dark:text-white text-slate-600">ms</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                    <span class="text-xs uppercase tracking-widest opacity-60 dark:text-white text-slate-600">Download</span>
                    <div class="text-4xl font-bold font-heading mt-2 text-emerald-400" id="dlResult">--</div>
                    <span class="text-xs opacity-50 dark:text-white text-slate-600">Mbps (Approx)</span>
                </div>
            </div>

            <button onclick="runSpeedTest()" id="btnSpeed" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-indigo-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="play" class="w-5 h-5"></i> START TEST
            </button>
            
            <p id="speedStatus" class="mt-4 text-sm opacity-60 italic dark:text-white text-slate-600 h-5"></p>
        </div>
    </div>
</div>