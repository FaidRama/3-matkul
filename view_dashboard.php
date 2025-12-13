<div id="view-dashboard" class="view-section">
    <div class="text-center py-10">
        <h3 class="text-4xl lg:text-5xl font-heading font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-950 to-blue-800 dark:from-white dark:to-gray-400">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
        <p class="opacity-70 text-lg font-light dark:text-white text-blue-900">Your secure workspace is ready for action.</p>
    </div>
    
    <!-- NEW CATEGORY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <!-- Conversion Tools Card -->
        <div class="glass-panel p-6 rounded-2xl border border-white/10 hover:border-blue-400/50 transition-all cursor-pointer group" onclick="document.getElementById('nav-docx').click()">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 rounded-xl bg-blue-500/20 text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <i data-lucide="refresh-cw" class="w-8 h-8"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold font-heading dark:text-white text-blue-950">Conversion Tools</h4>
                    <p class="text-sm opacity-60 dark:text-white text-blue-900">Transform your files effortlessly.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">DOCX → PDF</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">Image Compress</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">MP4 → MP3</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">Unit Converter</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">Base Converter</span>
            </div>
        </div>

        <!-- Utilities Card -->
        <div class="glass-panel p-6 rounded-2xl border border-white/10 hover:border-emerald-400/50 transition-all cursor-pointer group" onclick="document.getElementById('nav-qr').click()">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <i data-lucide="box" class="w-8 h-8"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold font-heading dark:text-white text-blue-950">Utilities</h4>
                    <p class="text-sm opacity-60 dark:text-white text-blue-900">Essential tools for daily tasks.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">QR Generator</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">BMI Calc</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">Markdown</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">Speed Test</span>
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold opacity-80 dark:text-white text-blue-900">World Clock</span>
            </div>
        </div>
    </div>
    
    <div class="mt-8">
        <div class="flex justify-between items-center mb-6">
            <h4 class="font-bold font-heading text-2xl flex items-center gap-3 dark:text-white text-blue-950">
                <div class="p-2 rounded-lg bg-blue-500/20 text-blue-900 dark:text-blue-300"><i data-lucide="clock" class="w-6 h-6"></i></div>
                Recent Activity
            </h4>
            <button onclick="location.reload()" class="text-xs px-4 py-2 rounded-full border border-white/20 hover:bg-white/10 flex items-center gap-2 font-medium transition-all hover:pr-5 group dark:text-white text-blue-900 dark:border-white/20 border-blue-900/30">
                <i data-lucide="refresh-cw" class="w-3 h-3 group-hover:rotate-180 transition-transform duration-500"></i> Refresh
            </button>
        </div>
        
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/10 dark:bg-black/20 backdrop-blur-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 font-heading border-b border-white/10 dark:text-white text-blue-900">
                    <tr><th class="px-6 py-4 opacity-70">Time</th><th class="px-6 py-4 opacity-70">Original File</th><th class="px-6 py-4 opacity-70">Action</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5 dark:text-white text-slate-800">
                    <?php if(empty($files)): ?>
                        <tr><td colspan="3" class="px-6 py-12 text-center opacity-40 italic">No recent files found.</td></tr>
                    <?php else: foreach($files as $base=>$types): 
                        $fileData = null; $actionBtn = "";
                        $ts = explode('_',$base)[0]; 
                        $date = is_numeric($ts) ? date("d M H:i", $ts) : "-";

                        if(isset($types['pdf'])) {
                            $d = $types['docx'] ?? ['filename' => 'Unknown DOCX'];
                            $p = $types['pdf']; $fileData = $d;
                            $actionBtn = '<a href="download.php?id='.$p['id'].'" class="text-xs bg-lime-400 hover:bg-lime-500 text-black border border-lime-500 dark:bg-emerald-500/20 dark:hover:bg-emerald-500 dark:text-emerald-300 dark:hover:text-white dark:border-emerald-500/50 px-3 py-1.5 rounded-lg flex items-center gap-2 w-fit font-bold transition-all"><i data-lucide="download" class="w-3 h-3"></i> Download PDF</a>';
                        } elseif(isset($types['png'])) {
                            $fileData = $types['png']; $fileData['filename'] = "QR Code Generated";
                            $actionBtn = '<a href="download.php?id='.$fileData['id'].'" class="text-xs bg-sky-400 hover:bg-sky-500 text-black border border-sky-500 dark:bg-sky-500/20 dark:hover:bg-sky-500 dark:text-sky-300 dark:hover:text-white dark:border-sky-500/50 px-3 py-1.5 rounded-lg flex items-center gap-2 w-fit font-bold transition-all"><i data-lucide="download" class="w-3 h-3"></i> Download QR</a>';
                        } elseif(isset($types['jpg'])) {
                            $fileData = $types['jpg'];
                            $actionBtn = '<a href="download.php?id='.$fileData['id'].'" class="text-xs bg-emerald-400 hover:bg-emerald-500 text-black border border-emerald-500 dark:bg-teal-500/20 dark:hover:bg-teal-500 dark:text-teal-300 dark:hover:text-white dark:border-teal-500/50 px-3 py-1.5 rounded-lg flex items-center gap-2 w-fit font-bold transition-all"><i data-lucide="download" class="w-3 h-3"></i> Download JPG</a>';
                        } elseif(isset($types['mp3'])) {
                            $fileData = $types['mp3'];
                            $actionBtn = '<a href="download.php?id='.$fileData['id'].'" class="text-xs bg-rose-400 hover:bg-rose-500 text-black border border-rose-500 dark:bg-pink-500/20 dark:hover:bg-pink-500 dark:text-pink-300 dark:hover:text-white dark:border-pink-500/50 px-3 py-1.5 rounded-lg flex items-center gap-2 w-fit font-bold transition-all"><i data-lucide="download" class="w-3 h-3"></i> Download MP3</a>';
                        }

                        if($fileData):
                    ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs opacity-60"><?= $date ?></td>
                        <td class="px-6 py-4 flex items-center gap-3 font-medium">
                            <i data-lucide="file" class="w-4 h-4 text-blue-900 dark:text-blue-400"></i> <?= htmlspecialchars($fileData['filename']) ?>
                        </td>
                        <td class="px-6 py-4"><?= $actionBtn ?></td>
                    </tr>
                    <?php endif; endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>