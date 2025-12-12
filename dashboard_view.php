<?php 
if(!defined('INDEX_LOADED')) { die("Direct access denied! Open index.php instead."); } 
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SecureTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['"Plus Jakarta Sans"', 'sans-serif'],
                        'body': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -50;
            background-color: #34495e;
            overflow: hidden;
        }

        .animated-bg::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1000%; 
            background: linear-gradient(to bottom, 
                rgb(101,91,239) 0%, 
                rgb(250,40,191) 20%, 
                rgb(255,70,101) 40%, 
                rgb(251,222,78) 60%, 
                rgb(0,251,234) 80%, 
                rgb(85,93,239) 100%
            );
            animation: anime 20s linear infinite alternate;
        }

        @keyframes anime {
            0% { transform: translateY(0); }
            50% { transform: translateY(-50%); } 
            100% { transform: translateY(0); }
        }

        body { font-family: 'Inter', sans-serif; color: white; }
        
        .glass-panel {
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-panel { background: rgba(0, 0, 0, 0.4); color: white; }
        html:not(.dark) .glass-panel {
            background: rgba(255, 255, 255, 0.65); color: #1f2937; 
            border-color: rgba(255, 255, 255, 0.6);
        }

        .glass-input { 
            width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; 
            transition: all 0.2s; outline: none;
        }
        .dark .glass-input { 
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: white; 
        }
        .dark .glass-input:focus { border-color: #fbd38d; background: rgba(0,0,0,0.5); }
        html:not(.dark) .glass-input { 
            background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.1); color: #333; 
        }
        html:not(.dark) .glass-input:focus { border-color: #6366f1; background: white; }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.2); font-weight: 700;
            border-left: 4px solid #fbde4e; 
        }
        html:not(.dark) .nav-item.active { background: rgba(255, 255, 255, 0.8); color: #4f46e5; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }

        /* --- SIDEBAR ANIMATION STYLES --- */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease-in-out;
        }

        @media (min-width: 1024px) {
            #sidebar.collapsed {
                width: 5rem;
            }
            #sidebar.collapsed .sidebar-label {
                opacity: 0; width: 0; margin-left: 0; pointer-events: none;
            }
            #sidebar.collapsed .nav-item {
                justify-content: center; padding-left: 0; padding-right: 0;
            }
            #sidebar.collapsed .logo-text { display: none; }
            #sidebar.collapsed .user-info { display: none; }
            #sidebar.collapsed .logo-container { margin-right: 0; }
        }

        .sidebar-label {
            transition: opacity 0.2s ease-in-out, width 0.2s ease-in-out, margin 0.2s ease-in-out;
            white-space: nowrap; overflow: hidden; width: auto; opacity: 1; margin-left: 0.75rem; 
        }
    </style>
</head>
<body class="overflow-hidden">

    <div class="animated-bg"></div>

    <div class="flex h-screen relative z-10">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-20 hidden lg:hidden backdrop-blur-sm"></div>
        
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-64 -translate-x-full lg:translate-x-0 glass-panel border-r border-white/10 flex flex-col h-full overflow-hidden shrink-0">
            <div class="h-20 flex items-center px-6 border-b border-white/10 overflow-hidden whitespace-nowrap">
                <div class="logo-container w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-300 to-pink-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg shrink-0 mr-3 transition-all">S</div>
                <span class="logo-text text-xl font-heading tracking-wide font-bold transition-opacity duration-300">SecureTools</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Dashboard</span>
                    <div class="absolute left-16 bg-black px-2 py-1 rounded text-xs hidden group-hover:block lg:group-hover:hidden whitespace-nowrap z-50">Dashboard</div>
                </button>
                <button onclick="switchTab('docx')" id="nav-docx" class="nav-item w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="file-text" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">DOCX to PDF</span>
                </button>
                <button onclick="switchTab('image')" id="nav-image" class="nav-item w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="image" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Image Compress</span>
                </button>
                <button onclick="switchTab('qr')" id="nav-qr" class="nav-item w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="qr-code" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">QR Generator</span>
                </button>
                <button onclick="switchTab('media')" id="nav-media" class="nav-item w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="music" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">MP4 to MP3</span>
                </button>
                <button onclick="switchTab('bmi')" id="nav-bmi" class="nav-item w-full flex items-center px-4 py-3 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="scale" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">BMI Calculator</span>
                </button>
            </nav>

            <div class="p-4 border-t border-white/10 bg-black/10 overflow-hidden">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold shrink-0">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </div>
                    <div class="user-info flex-1 min-w-0 ml-3 transition-opacity duration-300">
                        <p class="text-sm font-bold font-heading truncate"><?= htmlspecialchars($_SESSION['username']) ?></p>
                        <div class="flex items-center gap-1 text-xs opacity-70">
                            <div class="w-2 h-2 rounded-full bg-green-400"></div> Online
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-20 flex items-center justify-between px-6 glass-panel border-b border-white/10 z-10 shrink-0">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h2 id="page-title" class="text-xl font-heading font-bold tracking-wide text-white drop-shadow-md hidden md:block">DASHBOARD</h2>
                </div>

                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-white/20 transition-colors backdrop-blur-md bg-white/10 shadow-sm border border-white/20">
                        <i id="icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-yellow-300"></i>
                        <i id="icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-indigo-600"></i>
                    </button>
                    <a href="?logout=true" class="bg-red-500 hover:bg-red-600 text-white border border-red-400 px-5 py-2 rounded-full text-xs font-bold transition-all shadow-lg hover:shadow-red-500/50 flex items-center gap-2 font-heading">
                        <i data-lucide="log-out" class="w-3 h-3"></i> <span class="hidden sm:inline">LOGOUT</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 relative">
                <div class="max-w-5xl mx-auto space-y-6">

                    <div id="view-dashboard" class="view-section">
                        <div class="glass-panel rounded-2xl p-8 mb-6 text-center">
                            <h3 class="text-3xl font-heading font-bold mb-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
                            <p class="opacity-80">Your encrypted workspace is ready.</p>
                        </div>
                        <div class="glass-panel rounded-2xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-black/20">
                                <h4 class="font-bold font-heading text-xl flex items-center gap-2">
                                    <i data-lucide="clock" class="w-5 h-5"></i> Recent Files
                                </h4>
                                <button onclick="location.reload()" class="text-xs px-3 py-1 rounded border border-white/30 hover:bg-white/20 flex items-center gap-1 font-medium">
                                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Refresh
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs uppercase bg-black/10 font-heading">
                                        <tr><th class="px-6 py-3">Time</th><th class="px-6 py-3">Original</th><th class="px-6 py-3">Result</th></tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        <?php if(empty($files)): ?>
                                            <tr><td colspan="3" class="px-6 py-8 text-center opacity-50">No activity yet.</td></tr>
                                        <?php else: foreach($files as $base=>$pair): 
                                            $d=$pair['docx']??null; $p=$pair['pdf']??null;
                                            if(!$d && !$p) continue;
                                            $ts = explode('_',$base)[0]; 
                                            $date = is_numeric($ts) ? date("d M H:i", $ts) : "-";
                                        ?>
                                        <tr class="hover:bg-white/5">
                                            <td class="px-6 py-4 font-mono text-xs opacity-70"><?= $date ?></td>
                                            <td class="px-6 py-4 flex items-center gap-2">
                                                <i data-lucide="file" class="w-4 h-4 opacity-50"></i> <?= $d ? htmlspecialchars($d['filename']) : '-' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if($p): ?><a href="download.php?id=<?= $p['id'] ?>" class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded flex items-center gap-1 w-fit font-bold"><i data-lucide="download" class="w-3 h-3"></i> PDF</a><?php else: echo "Pending"; endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="view-docx" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 font-heading">
                                <i data-lucide="file-text" class="w-8 h-8 text-blue-400"></i> DOCX to PDF
                            </h3>
                            <form id="formDoc" class="space-y-6">
                                <div class="relative">
                                    <input type="file" id="fileDoc" accept=".docx" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-400 file:text-black hover:file:bg-yellow-300 cursor-pointer text-white dark:text-white text-gray-800 pl-12">
                                    <i data-lucide="upload" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading">
                                    <i data-lucide="zap" class="w-4 h-4"></i> CONVERT NOW
                                </button>
                            </form>
                            <div id="resDoc" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-image" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 font-heading">
                                <i data-lucide="image" class="w-8 h-8 text-pink-400"></i> Image Compress
                            </h3>
                            <form id="formImg" class="space-y-6">
                                <div class="relative">
                                    <input type="file" id="fileImg" accept="image/*" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-pink-500 file:text-white hover:file:bg-pink-400 cursor-pointer pl-12">
                                    <i data-lucide="upload" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading">
                                    <i data-lucide="minimize-2" class="w-4 h-4"></i> COMPRESS NOW
                                </button>
                            </form>
                            <div id="resImg" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-qr" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 font-heading">
                                <i data-lucide="qr-code" class="w-8 h-8 text-teal-400"></i> QR Generator
                            </h3>
                            <form id="formQr" class="space-y-6">
                                <div class="relative">
                                    <input type="url" id="inpUrl" placeholder="https://..." class="glass-input pl-12">
                                    <i data-lucide="link" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-400 hover:to-teal-400 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading">
                                    <i data-lucide="settings" class="w-4 h-4"></i> GENERATE QR
                                </button>
                            </form>
                            <div id="resQr" class="mt-6 text-center"></div>
                        </div>
                    </div>

                    <!-- BMI VIEW (UPDATED ENGLISH) -->
                    <div id="view-bmi" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 font-heading">
                                <i data-lucide="scale" class="w-8 h-8 text-orange-400"></i> BMI Calculator
                            </h3>

                            <div class="mb-6 p-4 rounded-xl bg-white/5 border border-white/10 text-sm">
                                <h4 class="font-bold flex items-center gap-2 mb-2 text-yellow-300 font-heading">
                                    <i data-lucide="info" class="w-4 h-4"></i> Formula Info
                                </h4>
                                <p class="opacity-80">
                                    BMI = Weight (kg) / (Height (m) x Height (m))
                                </p>
                            </div>

                            <form id="formBmi" class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative">
                                        <input type="number" id="bH" placeholder="Height (cm)" class="glass-input pl-10">
                                        <i data-lucide="ruler" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                                    </div>
                                    <div class="relative">
                                        <input type="number" id="bW" placeholder="Weight (kg)" class="glass-input pl-10">
                                        <i data-lucide="weight" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-400 hover:to-red-400 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading">
                                    <i data-lucide="calculator" class="w-4 h-4"></i> CALCULATE
                                </button>
                            </form>
                            <div id="resBmi" class="mt-6"></div>
                        </div>
                    </div>

                    <div id="view-media" class="view-section hidden">
                        <div class="glass-panel rounded-3xl p-8 max-w-xl mx-auto">
                            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 font-heading">
                                <i data-lucide="music" class="w-8 h-8 text-purple-400"></i> MP4 to MP3
                            </h3>
                            <form id="formMedia" class="space-y-6">
                                <div class="relative">
                                    <input type="file" id="fileMedia" accept="video/mp4, video/x-m4v, video/quicktime, video/x-matroska" class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-yellow-400 file:text-black hover:file:bg-yellow-300 cursor-pointer text-white dark:text-white text-gray-800 pl-12">
                                    <i data-lucide="film" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold shadow-lg transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading">
                                    <i data-lucide="music-2" class="w-4 h-4"></i> CONVERT TO MP3
                                </button>
                            </form>
                            <div id="resMedia" class="mt-6"></div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else { html.classList.remove('dark'); }
        
        function toggleTheme() {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isMobile = window.innerWidth < 1024; 

            if (isMobile) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }

        function switchTab(id) {
            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            document.getElementById('view-'+id).classList.remove('hidden');
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-'+id).classList.add('active');
            const titles = {'dashboard':'DASHBOARD','docx':'DOCX CONVERTER','image':'IMAGE COMPRESSOR','qr':'QR GENERATOR','bmi':'BMI CALCULATOR', 'media':'MP4 TO MP3'};
            document.getElementById('page-title').innerText = titles[id];
            
            if(window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('sidebar-overlay').classList.add('hidden');
            }
        }

        const API = "?endpoint=";
        const msg = (el, txt, err=false) => {
            el.innerHTML = `<div class="p-4 rounded-xl text-center font-bold ${err?'bg-red-500/20 text-red-200 border border-red-500/30':'bg-green-500/20 text-white border border-green-500/30'} flex flex-col items-center gap-2"><i data-lucide="${err?'alert-circle':'check-circle'}" class="w-6 h-6"></i><span>${txt}</span></div>`;
            lucide.createIcons();
        };

        // DOCX
        document.getElementById('formDoc').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resDoc');
            const f=document.getElementById('fileDoc').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); res.innerText="Converting...";
            try {
                const r=await fetch(API+'/doc/convert',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `<a href="${d.downloadUrl}" class="underline">Success! Download PDF</a>`);
            } catch(e){ msg(res, e, true); }
        };

        // IMAGE
        document.getElementById('formImg').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resImg');
            const f=document.getElementById('fileImg').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); res.innerText="Compressing...";
            try {
                const r=await fetch(API+'/image/compress',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `Saved ${((d.originalSize-d.compressedSize)/1024).toFixed(1)} KB! <a href="${d.downloadUrl}" class="underline ml-2">Download</a>`);
            } catch(e){ msg(res, e, true); }
        };

        // QR
        document.getElementById('formQr').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resQr'); res.innerText="Generating...";
            try {
                const r=await fetch(API+'/url/qr',{method:'POST',body:JSON.stringify({url:document.getElementById('inpUrl').value})}); const d=await r.json();
                if(d.error) throw d.error; res.innerHTML=`<img src="${d.downloadUrl}" class="w-32 mx-auto rounded mb-2 shadow-lg"><a href="${d.downloadUrl}" download="${d.fileName}" class="bg-white text-black px-3 py-1 rounded text-xs font-bold flex items-center gap-1 justify-center w-fit mx-auto"><i data-lucide="download" class="w-3 h-3"></i> DOWNLOAD</a>`;
                lucide.createIcons();
            } catch(e){ msg(res, e, true); }
        };

        // BMI
        document.getElementById('formBmi').onsubmit = async (e) => {
            e.preventDefault(); 
            const res = document.getElementById('resBmi'); 
            res.innerHTML = '<div class="text-center animate-pulse">Calculating...</div>';
            
            try {
                const r = await fetch(API+'/calc/bmi',{
                    method:'POST',
                    body:JSON.stringify({
                        heightCm: document.getElementById('bH').value, 
                        weightKg: document.getElementById('bW').value
                    })
                });
                const d = await r.json();
                if(d.error) throw d.error;

                res.innerHTML = `
                <div class="bg-white/10 border border-white/20 rounded-xl p-6 text-center shadow-lg">
                    <h4 class="text-4xl font-bold mb-2 font-heading">${d.bmi}</h4>
                    <p class="font-bold text-lg ${d.color} mb-4">${d.category}</p>
                    <div class="bg-black/20 rounded-lg p-3 text-sm opacity-90 border border-white/10">
                        <i data-lucide="lightbulb" class="w-4 h-4 inline-block mb-1 text-yellow-300"></i>
                        ${d.message}
                    </div>
                </div>`;
                lucide.createIcons();
            } catch(e){ 
                msg(res, "Error: " + e, true); 
            }
        };

        // MEDIA
        document.getElementById('formMedia').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resMedia'); const btn=document.querySelector('#formMedia button'); const f=document.getElementById('fileMedia').files[0]; 
            if(!f) return;
            if(f.size > 50*1024*1024) { msg(res, "File too large (>50MB).", true); return; }
            const fd=new FormData(); fd.append('file', f); 
            btn.disabled=true; btn.innerHTML=`<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Converting...`; btn.classList.add("opacity-50");
            res.innerHTML=`<div class="p-4 rounded-xl text-center bg-blue-500/20 text-blue-200 border border-blue-500/30 animate-pulse font-bold">Processing...</div>`;
            try {
                const r=await fetch(API+'/media/convert',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `✅ Success! <a href="${d.downloadUrl}" class="underline font-bold text-green-400">Download MP3</a>`);
            } catch(e) { msg(res, e, true); }
            finally { btn.disabled=false; btn.innerHTML=`<i data-lucide="music-2" class="w-4 h-4"></i> CONVERT TO MP3`; btn.classList.remove("opacity-50"); lucide.createIcons(); }
        };
    </script>
</body>
</html>