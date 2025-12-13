<div id="view-number" class="view-section hidden">
    <div class="max-w-2xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-violet-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-violet-500/30">
                <i data-lucide="binary" class="w-10 h-10 text-violet-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">Base Converter</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Real-time conversion between number systems.</p>
        </div>

        <div class="space-y-6">
            <div class="relative group">
                <label class="text-xs font-bold uppercase opacity-60 mb-1 block">Decimal (10)</label>
                <input type="text" id="numDec" oninput="convertNumber('dec')" placeholder="0-9" class="glass-input pl-4 font-mono text-lg">
            </div>
            <div class="relative group">
                <label class="text-xs font-bold uppercase opacity-60 mb-1 block">Binary (2)</label>
                <input type="text" id="numBin" oninput="convertNumber('bin')" placeholder="0-1" class="glass-input pl-4 font-mono text-lg">
            </div>
            <div class="relative group">
                <label class="text-xs font-bold uppercase opacity-60 mb-1 block">Octal (8)</label>
                <input type="text" id="numOct" oninput="convertNumber('oct')" placeholder="0-7" class="glass-input pl-4 font-mono text-lg">
            </div>
            <div class="relative group">
                <label class="text-xs font-bold uppercase opacity-60 mb-1 block">Hexadecimal (16)</label>
                <input type="text" id="numHex" oninput="convertNumber('hex')" placeholder="0-9, A-F" class="glass-input pl-4 font-mono text-lg uppercase">
            </div>
        </div>
    </div>
</div>