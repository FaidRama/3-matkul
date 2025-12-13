<div id="view-qr" class="view-section hidden">
    <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-sky-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-sky-500/30">
                <i data-lucide="qr-code" class="w-10 h-10 text-sky-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">QR Generator</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Create custom QR codes for any URL.</p>
        </div>
        <form id="formQr" class="space-y-6">
            <div class="relative group">
                <input type="url" id="inpUrl" placeholder="Enter website URL (https://...)" class="glass-input pl-14 group-hover:border-sky-500/50 transition-colors">
                <i data-lucide="link" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-sky-400/70 group-hover:text-sky-400 transition-colors"></i>
            </div>
            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-sky-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="settings" class="w-5 h-5"></i> GENERATE QR CODE
            </button>
        </form>
        <div id="resQr" class="mt-8 text-center"></div>
    </div>
</div>