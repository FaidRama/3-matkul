<div id="view-docx" class="view-section hidden">
    <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-amber-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-amber-500/30">
                <i data-lucide="file-text" class="w-10 h-10 text-amber-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">DOCX to PDF</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Convert your documents instantly and securely.</p>
        </div>
        <form id="formDoc" class="space-y-6">
            <div class="relative group">
                <input type="file" id="fileDoc" accept=".docx" class="glass-input file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-white hover:file:bg-amber-600 cursor-pointer pl-14 group-hover:border-amber-500/50 transition-colors">
                <i data-lucide="upload" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-amber-400/70 group-hover:text-amber-400 transition-colors"></i>
            </div>
            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold shadow-lg shadow-amber-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="zap" class="w-5 h-5"></i> CONVERT DOCUMENT
            </button>
        </form>
        <div id="resDoc" class="mt-8"></div>
    </div>
</div>