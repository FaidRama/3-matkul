<div id="view-bmi" class="view-section hidden">
     <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-orange-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-orange-500/30">
                <i data-lucide="scale" class="w-10 h-10 text-orange-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">BMI Calculator</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Check your Body Mass Index scientifically.</p>
        </div>

        <div class="mb-8 p-5 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-sm flex items-start gap-4">
            <div class="p-2 bg-orange-500/20 rounded-lg shrink-0 text-orange-400"><i data-lucide="info" class="w-5 h-5"></i></div>
            <div>
                <h4 class="font-bold text-orange-300 font-heading mb-1 dark:text-orange-300 text-orange-600">Formula Info</h4>
                <p class="opacity-80 leading-relaxed dark:text-white text-slate-700">BMI = Weight (kg) / (Height (m) x Height (m))</p>
            </div>
        </div>

        <form id="formBmi" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div class="relative group">
                    <input type="number" id="bH" placeholder="Height (cm)" class="glass-input pl-14 group-hover:border-orange-500/50 transition-colors">
                    <i data-lucide="ruler" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-orange-400/70 group-hover:text-orange-400 transition-colors"></i>
                </div>
                <div class="relative group">
                    <input type="number" id="bW" placeholder="Weight (kg)" class="glass-input pl-14 group-hover:border-orange-500/50 transition-colors">
                    <i data-lucide="weight" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-orange-400/70 group-hover:text-orange-400 transition-colors"></i>
                </div>
            </div>
            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold shadow-lg shadow-orange-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="calculator" class="w-5 h-5"></i> CALCULATE BMI
            </button>
        </form>
        <div id="resBmi" class="mt-8"></div>
    </div>
</div>