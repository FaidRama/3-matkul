<div id="view-image" class="view-section hidden">
    <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-emerald-500/30">
                <i data-lucide="image" class="w-10 h-10 text-emerald-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">Image Compress</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Reduce file size without losing quality.</p>
        </div>
        <form id="formImg" class="space-y-6">
            <div class="relative group">
                <input type="file" id="fileImg" accept="image/*" class="glass-input file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500 file:text-white hover:file:bg-emerald-600 cursor-pointer pl-14 group-hover:border-emerald-500/50 transition-colors">
                <i data-lucide="upload" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-emerald-400/70 group-hover:text-emerald-400 transition-colors"></i>
            </div>
            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="minimize-2" class="w-5 h-5"></i> COMPRESS NOW
            </button>
        </form>
        <div id="resImg" class="mt-8"></div>
    </div>
</div>