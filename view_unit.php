<div id="view-unit" class="view-section hidden">
    <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-cyan-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-cyan-500/30">
                <i data-lucide="flask-conical" class="w-10 h-10 text-cyan-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">Unit Converter</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Easily convert between scientific units.</p>
        </div>

        <form id="formUnit" class="space-y-6">
            <div>
                <label class="text-sm font-bold opacity-70 mb-2 block">Category</label>
                <select id="unitCategory" onchange="updateUnitOptions()" class="glass-select">
                    <option value="length">Length</option>
                    <option value="mass">Mass / Weight</option>
                    <option value="temp">Temperature</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold opacity-70 mb-2 block">From</label>
                    <select id="unitFrom" class="glass-select"></select>
                </div>
                <div>
                    <label class="text-sm font-bold opacity-70 mb-2 block">To</label>
                    <select id="unitTo" class="glass-select"></select>
                </div>
            </div>

            <div>
                <label class="text-sm font-bold opacity-70 mb-2 block">Value</label>
                <input type="number" id="unitValue" placeholder="Enter value..." class="glass-input">
            </div>

            <button type="button" onclick="calculateUnit()" class="w-full py-4 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="arrow-right-left" class="w-5 h-5"></i> CONVERT
            </button>
        </form>

        <div id="resUnit" class="mt-8 text-center text-2xl font-bold font-heading dark:text-white text-slate-800"></div>
    </div>
</div>