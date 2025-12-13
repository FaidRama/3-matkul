<div id="view-media" class="view-section hidden">
     <div class="max-w-xl mx-auto py-10">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-rose-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-rose-500/30">
                <i data-lucide="music" class="w-10 h-10 text-rose-400"></i>
            </div>
            <h3 class="text-3xl font-heading font-bold mb-2 dark:text-white text-slate-800">MP4 to MP3</h3>
            <p class="opacity-60 dark:text-white text-slate-600">Extract audio from your video files.</p>
        </div>
        <form id="formMedia" class="space-y-6">
            <div class="relative group">
                <input type="file" id="fileMedia" accept="video/mp4, video/x-m4v, video/quicktime, video/x-matroska" class="glass-input file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-500 file:text-white hover:file:bg-rose-600 cursor-pointer text-white pl-14 group-hover:border-rose-500/50 transition-colors">
                <i data-lucide="film" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-rose-400/70 group-hover:text-rose-400 transition-colors"></i>
            </div>
            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold shadow-lg shadow-rose-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                <i data-lucide="music-2" class="w-5 h-5"></i> CONVERT TO MP3
            </button>
        </form>
        <div id="resMedia" class="mt-8"></div>
    </div>
</div>