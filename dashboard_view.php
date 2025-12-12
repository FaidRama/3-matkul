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
    
    <!-- Load Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- Load Marked.js for Markdown Preview -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <!-- Load jsVectorMap for World Clock -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/js/jsvectormap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['"Plus Jakarta Sans"', 'sans-serif'],
                        'body': ['Inter', 'sans-serif'],
                    },
                    transitionProperty: {
                        'bg': 'background, background-color, background-image',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* --- DYNAMIC BACKGROUND --- */
        #main-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100vh;
            z-index: -50;
            transition: background 0.7s ease-in-out;
            background-size: 200% 200%;
            animation: gradientMove 15s ease infinite;
        }

        #particles-js {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100vh;
            z-index: -40; 
            pointer-events: none;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- LIQUID GLASS iOS 26 EFFECT --- */
        .glass-panel {
            background: rgba(255, 255, 255, 0.002);
            backdrop-filter: blur(2px) saturate(105%);
            -webkit-backdrop-filter: blur(2px) saturate(105%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            border-left: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.02), inset 0 0 20px rgba(255, 255, 255, 0.01);
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .glass-panel {
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(255, 255, 255, 0.05);
            border-top-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2), inset 0 0 0 1px rgba(255, 255, 255, 0.01);
        }

        html:not(.dark) .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(31, 38, 135, 0.02), inset 0 0 20px rgba(255, 255, 255, 0.05);
            color: #1e293b;
        }

        /* Inputs */
        .glass-input, .glass-select, .glass-textarea { 
            width: 100%; padding: 0.85rem 1.2rem; border-radius: 16px; 
            transition: all 0.2s; outline: none;
            backdrop-filter: blur(2px);
        }
        .dark .glass-input, .dark .glass-select, .dark .glass-textarea { 
            background: rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.05); color: white; 
        }
        .dark .glass-input:focus, .dark .glass-select:focus, .dark .glass-textarea:focus { border-color: rgba(255,255,255,0.3); background: rgba(0,0,0,0.2); box-shadow: 0 0 15px rgba(255,255,255,0.02); }
        
        html:not(.dark) .glass-input, html:not(.dark) .glass-select, html:not(.dark) .glass-textarea { 
            background: rgba(255,255,255,0.15); border: 1px solid rgba(0,0,0,0.05); color: #333; 
        }
        html:not(.dark) .glass-input:focus, html:not(.dark) .glass-select:focus, html:not(.dark) .glass-textarea:focus { border-color: #6366f1; background: rgba(255,255,255,0.4); box-shadow: 0 0 15px rgba(99, 102, 241, 0.05); }

        .dark option { background-color: #1e293b; color: white; }

        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }

        #sidebar { transition: width 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.3s ease-in-out; }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.05); font-weight: 700;
            border-right: 3px solid white; 
            border-radius: 12px;
        }
        html:not(.dark) .nav-item.active { background: rgba(0, 0, 0, 0.02); border-color: #333; }
        
        @media (min-width: 1024px) {
            #sidebar.collapsed { width: 5.5rem; }
            #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; margin: 0; pointer-events: none; }
            #sidebar.collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
            #sidebar.collapsed .logo-text, #sidebar.collapsed .user-info { display: none; }
            #sidebar.collapsed .category-header { display: none; }
        }
        .sidebar-label { transition: all 0.3s ease; white-space: nowrap; overflow: hidden; opacity: 1; margin-left: 0.75rem; }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.2); border-radius: 20px; }

        /* Map Styles */
        .jvm-zoom-btn { background-color: #3b82f6 !important; padding: 2px; border-radius: 4px; color: white; }
        .jvm-container { background: transparent !important; }
        .jvm-region { 
            fill: rgba(255,255,255,0.2); 
            stroke: rgba(255,255,255,0.1); 
            stroke-width: 0.5; 
            transition: fill 0.2s; 
            cursor: pointer !important;
        }
        .dark .jvm-region { fill: rgba(255,255,255,0.1); }
        .jvm-region:hover { fill: rgba(59, 130, 246, 0.5) !important; }
        
        .jvm-tooltip {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(4px);
            color: #ffffff !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5) !important;
            z-index: 99999 !important;
            white-space: nowrap;
        }

        /* Markdown Styles */
        .markdown-preview h1 { font-size: 2em; font-weight: bold; margin-bottom: 0.5em; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.2em; }
        .markdown-preview h2 { font-size: 1.5em; font-weight: bold; margin-bottom: 0.5em; }
        .markdown-preview h3 { font-size: 1.25em; font-weight: bold; margin-bottom: 0.5em; }
        .markdown-preview p { margin-bottom: 1em; line-height: 1.6; }
        .markdown-preview ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1em; }
        .markdown-preview ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1em; }
        .markdown-preview code { background: rgba(125,125,125,0.2); padding: 0.2em 0.4em; border-radius: 4px; font-family: monospace; }
        .markdown-preview pre { background: rgba(0,0,0,0.3); padding: 1em; border-radius: 8px; overflow-x: auto; margin-bottom: 1em; }
        .markdown-preview blockquote { border-left: 4px solid rgba(255,255,255,0.2); padding-left: 1em; font-style: italic; margin-bottom: 1em; opacity: 0.8; }
        .markdown-preview strong { font-weight: bold; color: inherit; }
        .markdown-preview em { font-style: italic; }
        .markdown-preview hr { border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 2em 0; }
    </style>
</head>
<body class="overflow-hidden dark:text-white text-slate-800 transition-colors duration-500">

    <!-- DYNAMIC BACKGROUND CONTAINER -->
    <div id="main-bg"></div>
    
    <!-- PARTICLES CONTAINER -->
    <div id="particles-js"></div>

    <div class="flex h-screen relative z-10">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-20 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300"></div>
        
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-72 -translate-x-full lg:translate-x-0 glass-panel border-r-0 m-4 mb-4 mt-4 mr-0 flex flex-col overflow-hidden shrink-0 shadow-2xl">
            <div class="h-24 flex items-center px-6 border-b border-white/10 overflow-hidden whitespace-nowrap shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br dark:from-white/20 dark:to-white/5 from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-inner shrink-0 mr-3 border border-white/20">
                    <span class="font-heading">S</span>
                </div>
                <span class="logo-text text-xl font-heading tracking-wide font-bold transition-opacity duration-300 dark:text-white text-slate-800">SecureTools</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <!-- MAIN -->
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Dashboard</span>
                </button>

                <!-- CONVERSION TOOLS -->
                <div class="px-4 pt-4 pb-2 text-xs uppercase opacity-50 font-bold dark:text-white text-slate-800 category-header">Conversion Tools</div>
                
                <button onclick="switchTab('docx')" id="nav-docx" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="file-text" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">DOCX to PDF</span>
                </button>
                <button onclick="switchTab('image')" id="nav-image" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="image" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Image Compress</span>
                </button>
                <button onclick="switchTab('media')" id="nav-media" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="music" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">MP4 to MP3</span>
                </button>
                <button onclick="switchTab('unit')" id="nav-unit" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="flask-conical" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Unit Converter</span>
                </button>
                <button onclick="switchTab('number')" id="nav-number" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="binary" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Number System</span>
                </button>

                <!-- UTILITIES -->
                <div class="px-4 pt-4 pb-2 text-xs uppercase opacity-50 font-bold dark:text-white text-slate-800 category-header">Utilities</div>

                <button onclick="switchTab('qr')" id="nav-qr" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="qr-code" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">QR Generator</span>
                </button>
                <button onclick="switchTab('bmi')" id="nav-bmi" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="scale" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">BMI Calculator</span>
                </button>
                <button onclick="switchTab('markdown')" id="nav-markdown" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="file-code" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Markdown Live</span>
                </button>
                <button onclick="switchTab('speed')" id="nav-speed" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="activity" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Speed Test</span>
                </button>
                <!-- NEW: WORLD CLOCK BUTTON -->
                <button onclick="switchTab('worldclock')" id="nav-worldclock" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="globe" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">World Clock</span>
                </button>
            </nav>

            <div class="p-5 border-t border-white/10 bg-black/5 overflow-hidden shrink-0">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-400 to-purple-500 flex items-center justify-center font-bold shrink-0 shadow-lg border-2 border-white/20 text-white">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </div>
                    <div class="user-info flex-1 min-w-0 ml-3 transition-opacity duration-300">
                        <p class="text-sm font-bold font-heading truncate dark:text-white text-slate-800"><?= htmlspecialchars($_SESSION['username']) ?></p>
                        <div class="flex items-center gap-1 text-xs opacity-70">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden p-4 lg:p-6 gap-6">
            <!-- Header -->
            <header class="h-20 flex items-center justify-between px-8 glass-panel z-10 shrink-0 shadow-lg">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="p-2.5 rounded-xl hover:bg-white/10 transition-colors dark:text-white text-slate-800">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h2 id="page-title" class="text-2xl font-heading font-extrabold tracking-tight drop-shadow-sm hidden md:block dark:text-white text-slate-800">DASHBOARD</h2>
                </div>

                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" class="p-3 rounded-full hover:bg-white/20 transition-all active:scale-95 bg-white/5 border border-white/10 shadow-sm group dark:text-white text-slate-800">
                        <i id="icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-yellow-300 group-hover:rotate-45 transition-transform"></i>
                        <i id="icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-indigo-600 group-hover:-rotate-12 transition-transform"></i>
                    </button>
                    <a href="?logout=true" class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white border border-white/20 px-6 py-2.5 rounded-full text-xs font-bold transition-all shadow-lg hover:shadow-red-500/30 flex items-center gap-2 font-heading tracking-wider active:scale-95">
                        <i data-lucide="log-out" class="w-3 h-3"></i> <span class="hidden sm:inline">LOGOUT</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 glass-panel overflow-y-auto p-6 lg:p-10 relative shadow-2xl">
                <div class="max-w-4xl mx-auto space-y-8 animate-fade-in-up">

                    <div id="view-dashboard" class="view-section">
                        <div class="text-center py-10">
                            <h3 class="text-4xl lg:text-5xl font-heading font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-950 to-blue-800 dark:from-white dark:to-gray-400">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
                            <p class="opacity-70 text-lg font-light dark:text-white text-blue-900">Your secure workspace is ready for action.</p>
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

                    <!-- DOCX VIEW -->
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

                    <!-- IMAGE VIEW -->
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

                    <!-- QR VIEW -->
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

                    <!-- BMI VIEW -->
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

                    <!-- MEDIA VIEW -->
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

                    <!-- 1. SCIENTIFIC UNIT CALCULATOR -->
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

                    <!-- 2. NUMBER SYSTEM CALCULATOR -->
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

                    <!-- 3. MARKDOWN LIVE PREVIEW -->
                    <div id="view-markdown" class="view-section hidden h-full">
                        <div class="flex flex-col h-[calc(100vh-10rem)]">
                            <div class="text-center mb-6 shrink-0">
                                <h3 class="text-2xl font-heading font-bold dark:text-white text-slate-800 flex items-center justify-center gap-2">
                                    <i data-lucide="file-code" class="w-6 h-6 text-fuchsia-400"></i> Markdown Live Preview
                                </h3>
                            </div>
                            
                            <!-- Help Section -->
                            <div class="mb-4 p-4 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 text-sm">
                                <h4 class="font-bold flex items-center gap-2 mb-2 text-fuchsia-400 font-heading">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i> Markdown Cheat Sheet
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 opacity-80">
                                    <div><code class="text-fuchsia-300"># Title</code> -> H1</div>
                                    <div><code class="text-fuchsia-300">## Title</code> -> H2</div>
                                    <div><code class="text-fuchsia-300">**Bold**</code> -> Bold</div>
                                    <div><code class="text-fuchsia-300">*Italic*</code> -> Italic</div>
                                    <div><code class="text-fuchsia-300">- List</code> -> List Item</div>
                                    <div><code class="text-fuchsia-300">1. List</code> -> Ordered</div>
                                    <div><code class="text-fuchsia-300">`Code`</code> -> Inline Code</div>
                                    <div><code class="text-fuchsia-300">> Quote</code> -> Blockquote</div>
                                </div>
                            </div>

                            <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-4 h-full">
                                <!-- Editor -->
                                <div class="flex flex-col">
                                    <label class="text-sm font-bold opacity-70 mb-2">Editor</label>
                                    <textarea id="mdInput" oninput="renderMarkdown()" class="glass-textarea flex-1 font-mono text-sm resize-none p-4 leading-relaxed" placeholder="# Write markdown here..."></textarea>
                                </div>
                                
                                <!-- Preview -->
                                <div class="flex flex-col">
                                    <label class="text-sm font-bold opacity-70 mb-2">Preview</label>
                                    <div id="mdOutput" class="glass-panel flex-1 p-6 overflow-y-auto markdown-preview bg-white/5 dark:bg-black/20"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SPEED TEST (RESTORED) -->
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
                    
                    <!-- 5. WORLD CLOCK (RESTORED MAP ONLY VIEW) -->
                    <div id="view-worldclock" class="view-section hidden h-full">
                        <div class="flex flex-col h-full">
                            <div class="text-center mb-6 shrink-0">
                                <h3 class="text-2xl font-heading font-bold dark:text-white text-slate-800 flex items-center justify-center gap-2">
                                    <i data-lucide="globe" class="w-6 h-6 text-teal-400"></i> World Clock
                                </h3>
                                <p class="opacity-60 dark:text-white text-slate-600">Click a country to see current time.</p>
                            </div>
                            
                            <div class="flex-1 glass-panel p-4 relative overflow-hidden flex flex-col rounded-2xl">
                                <!-- Digital Clock Overlay -->
                                <div class="absolute top-4 left-1/2 transform -translate-x-1/2 z-10 bg-black/60 backdrop-blur-md px-8 py-4 rounded-2xl border border-white/20 text-center shadow-2xl pointer-events-none">
                                    <div id="selectedCountry" class="text-xs font-bold uppercase tracking-widest text-teal-300 mb-1">SELECT A COUNTRY</div>
                                    <div id="digitalClock" class="text-5xl font-mono font-bold text-white tracking-widest">--:--:--</div>
                                    <div id="currentDate" class="text-sm text-white/70 mt-1">--</div>
                                </div>

                                <!-- Map Container -->
                                <div id="world-map" class="w-full h-full" style="min-height: 400px; position: relative; z-index: 1;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // --- THEME & BACKGROUND LOGIC ---
        const bgEl = document.getElementById('main-bg');
        
        const themes = {
            'dashboard': { dark: 'linear-gradient(135deg, #1e3a8a 0%, #000000 100%)', light: 'linear-gradient(135deg, #dbeafe 0%, #FFFFFF 100%)' },
            'docx': { dark: 'linear-gradient(135deg, #451a03 0%, #000000 100%)', light: 'linear-gradient(135deg, #FEF3C7 0%, #FFFFFF 100%)' },
            'image': { dark: 'linear-gradient(135deg, #064E3B 0%, #000000 100%)', light: 'linear-gradient(135deg, #D1FAE5 0%, #FFFFFF 100%)' },
            'qr': { dark: 'linear-gradient(135deg, #0C4A6E 0%, #000000 100%)', light: 'linear-gradient(135deg, #E0F2FE 0%, #FFFFFF 100%)' },
            'media': { dark: 'linear-gradient(135deg, #881337 0%, #000000 100%)', light: 'linear-gradient(135deg, #FFE4E6 0%, #FFFFFF 100%)' },
            'bmi': { dark: 'linear-gradient(135deg, #7C2D12 0%, #000000 100%)', light: 'linear-gradient(135deg, #FFEDD5 0%, #FFFFFF 100%)' },
            'unit': { dark: 'linear-gradient(135deg, #083344 0%, #000000 100%)', light: 'linear-gradient(135deg, #CFFAFE 0%, #FFFFFF 100%)' }, 
            'number': { dark: 'linear-gradient(135deg, #2e1065 0%, #000000 100%)', light: 'linear-gradient(135deg, #ede9fe 0%, #FFFFFF 100%)' },
            'markdown': { dark: 'linear-gradient(135deg, #701a75 0%, #000000 100%)', light: 'linear-gradient(135deg, #fae8ff 0%, #FFFFFF 100%)' },
            'speed': { dark: 'linear-gradient(135deg, #312e81 0%, #000000 100%)', light: 'linear-gradient(135deg, #e0e7ff 0%, #FFFFFF 100%)' },
            'worldclock': { dark: 'linear-gradient(135deg, #115e59 0%, #000000 100%)', light: 'linear-gradient(135deg, #ccfbf1 0%, #FFFFFF 100%)' } // Teal
        };

        let currentTab = 'dashboard';

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateBackground();

        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            updateBackground();
            // Re-render map if it exists to fix colors
            if(window.mapInstance) {
                // Simple hack: reload page or destroy/re-init. For simplicity here we just reload
                // A better way is to update map params.
            }
        }

        function updateBackground() {
            const isDark = document.documentElement.classList.contains('dark');
            const gradient = isDark ? themes[currentTab].dark : themes[currentTab].light;
            bgEl.style.background = gradient;
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
            currentTab = id;
            updateBackground();

            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            const activeView = document.getElementById('view-'+id);
            activeView.classList.remove('hidden');
            
            activeView.classList.remove('animate-fade-in-up');
            void activeView.offsetWidth; 
            activeView.classList.add('animate-fade-in-up');

            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-'+id).classList.add('active');
            
            const titles = {
                'dashboard':'DASHBOARD','docx':'DOCX TO PDF','image':'IMAGE COMPRESS',
                'qr':'QR GENERATOR','bmi':'BMI CALCULATOR', 'media':'MP4 TO MP3',
                'unit': 'UNIT CONVERTER', 'number': 'NUMBER SYSTEM', 'markdown': 'MARKDOWN LIVE', 'speed': 'SPEED TEST',
                'worldclock': 'WORLD CLOCK'
            };
            document.getElementById('page-title').innerText = titles[id];
            
            if(window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('sidebar-overlay').classList.add('hidden');
            }
            
            // Initialize Map only when tab is shown to ensure correct sizing
            if(id === 'worldclock') {
                if(!window.mapInitialized) {
                    initWorldMap();
                    window.mapInitialized = true;
                } else if(window.worldMapInstance) {
                    // Update size when showing tab
                    setTimeout(() => window.worldMapInstance.updateSize(), 100); 
                }
            }
        }

        // PARTICLES INIT
        particlesJS("particles-js", {
            "particles": { "number": { "value": 80 }, "color": { "value": "#ffffff" }, "shape": { "type": "circle" }, "opacity": { "value": 0.3 }, "size": { "value": 3 }, "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.2, "width": 1 }, "move": { "enable": true, "speed": 3 } },
            "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" } } },
            "retina_detect": true
        });

        const API = "?endpoint=";
        const msg = (el, txt, err=false) => {
            const color = err ? 'red' : 'emerald';
            const icon = err ? 'alert-circle' : 'check-circle';
            el.innerHTML = `<div class="animate-bounce-in p-4 rounded-xl text-center font-bold bg-${color}-500/20 text-${color}-200 border border-${color}-500/30 flex flex-col items-center gap-2 shadow-lg backdrop-blur-md"><i data-lucide="${icon}" class="w-8 h-8 text-${color}-400"></i><span>${txt}</span></div>`;
            lucide.createIcons();
        };

        document.getElementById('formDoc').onsubmit = async (e) => { e.preventDefault(); const res=document.getElementById('resDoc'); const f=document.getElementById('fileDoc').files[0]; if(!f) return; const fd=new FormData(); fd.append('file', f); res.innerHTML = '<div class="text-center animate-pulse opacity-70">Converting...</div>'; try { const r=await fetch(API+'/doc/convert',{method:'POST',body:fd}); const d=await r.json(); if(d.error) throw d.error; msg(res, `<a href="${d.downloadUrl}" class="underline decoration-2 underline-offset-4 hover:text-white transition-colors">Success! Download PDF</a>`); } catch(e){ msg(res, e, true); } };
        document.getElementById('formImg').onsubmit = async (e) => { e.preventDefault(); const res=document.getElementById('resImg'); const f=document.getElementById('fileImg').files[0]; if(!f) return; const fd=new FormData(); fd.append('file', f); res.innerHTML = '<div class="text-center animate-pulse opacity-70">Compressing...</div>'; try { const r=await fetch(API+'/image/compress',{method:'POST',body:fd}); const d=await r.json(); if(d.error) throw d.error; msg(res, `Saved ${((d.originalSize-d.compressedSize)/1024).toFixed(1)} KB! <a href="${d.downloadUrl}" class="underline ml-1">Download</a>`); } catch(e){ msg(res, e, true); } };
        document.getElementById('formQr').onsubmit = async (e) => { e.preventDefault(); const res=document.getElementById('resQr'); res.innerHTML = '<div class="text-center animate-pulse opacity-70">Generating...</div>'; try { const r=await fetch(API+'/url/qr',{method:'POST',body:JSON.stringify({url:document.getElementById('inpUrl').value})}); const d=await r.json(); if(d.error) throw d.error; res.innerHTML=`<div class="bg-white/10 p-6 rounded-2xl border border-white/20 inline-block shadow-2xl backdrop-blur-xl animate-fade-in-up"><img src="${d.downloadUrl}" class="w-40 mx-auto rounded-lg mb-4 shadow-lg border border-white/10"><a href="${d.downloadUrl}" download="${d.fileName}" class="bg-white text-slate-900 px-6 py-2 rounded-full text-xs font-bold flex items-center gap-2 justify-center w-full hover:bg-slate-200 transition-colors"><i data-lucide="download" class="w-4 h-4"></i> SAVE IMAGE</a></div>`; lucide.createIcons(); } catch(e){ msg(res, e, true); } };
        document.getElementById('formBmi').onsubmit = async (e) => { e.preventDefault(); const res = document.getElementById('resBmi'); res.innerHTML = '<div class="text-center animate-pulse opacity-70">Calculating...</div>'; try { const r = await fetch(API+'/calc/bmi',{ method:'POST', body:JSON.stringify({ heightCm: document.getElementById('bH').value, weightKg: document.getElementById('bW').value }) }); const d = await r.json(); if(d.error) throw d.error; res.innerHTML = `<div class="bg-white/10 border border-white/20 rounded-2xl p-8 text-center shadow-2xl backdrop-blur-xl animate-fade-in-up"><span class="text-sm uppercase tracking-widest opacity-60 mb-2 block">Your BMI Score</span><h4 class="text-6xl font-bold mb-2 font-heading tracking-tighter">${d.bmi}</h4><div class="inline-block px-4 py-1 rounded-full bg-white/10 border border-white/10 mb-6"><p class="font-bold text-lg ${d.color}">${d.category}</p></div><div class="bg-black/20 rounded-xl p-4 text-sm opacity-90 border border-white/5 text-left flex gap-3"><i data-lucide="lightbulb" class="w-5 h-5 shrink-0 text-yellow-300 mt-0.5"></i><span class="leading-relaxed">${d.message}</span></div></div>`; lucide.createIcons(); } catch(e){ msg(res, "Error: " + e, true); } };
        document.getElementById('formMedia').onsubmit = async (e) => { e.preventDefault(); const res=document.getElementById('resMedia'); const btn=document.querySelector('#formMedia button'); const f=document.getElementById('fileMedia').files[0]; if(!f) return; if(f.size > 50*1024*1024) { msg(res, "File too large (>50MB).", true); return; } const fd=new FormData(); fd.append('file', f); btn.disabled=true; btn.innerHTML=`<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Converting...`; btn.classList.add("opacity-50", "cursor-not-allowed"); res.innerHTML=`<div class="p-4 rounded-xl text-center bg-blue-500/20 text-blue-200 border border-blue-500/30 animate-pulse font-bold">Processing Video...</div>`; try { const r=await fetch(API+'/media/convert',{method:'POST',body:fd}); const d=await r.json(); if(d.error) throw d.error; msg(res, `✅ Success! <a href="${d.downloadUrl}" class="underline font-bold text-green-400">Download MP3</a>`); } catch(e) { msg(res, e, true); } finally { btn.disabled=false; btn.innerHTML=`<i data-lucide="music-2" class="w-5 h-5"></i> CONVERT TO MP3`; btn.classList.remove("opacity-50", "cursor-not-allowed"); lucide.createIcons(); } };

        /* --- NEW: UNIT CONVERTER LOGIC --- */
        const units = {
            length: ['Meters', 'Kilometers', 'Centimeters', 'Millimeters', 'Inches', 'Feet', 'Yards', 'Miles'],
            mass: ['Kilograms', 'Grams', 'Milligrams', 'Pounds', 'Ounces'],
            temp: ['Celsius', 'Fahrenheit', 'Kelvin']
        };
        const rates = {
            length: { Meters:1, Kilometers:0.001, Centimeters:100, Millimeters:1000, Inches:39.3701, Feet:3.28084, Yards:1.09361, Miles:0.000621371 },
            mass: { Kilograms:1, Grams:1000, Milligrams:1000000, Pounds:2.20462, Ounces:35.274 }
        };

        function updateUnitOptions() {
            const cat = document.getElementById('unitCategory').value;
            const from = document.getElementById('unitFrom');
            const to = document.getElementById('unitTo');
            from.innerHTML = ''; to.innerHTML = '';
            units[cat].forEach(u => {
                from.add(new Option(u, u));
                to.add(new Option(u, u));
            });
        }
        // Initialize
        updateUnitOptions();

        function calculateUnit() {
            const cat = document.getElementById('unitCategory').value;
            const val = parseFloat(document.getElementById('unitValue').value);
            const from = document.getElementById('unitFrom').value;
            const to = document.getElementById('unitTo').value;
            const resEl = document.getElementById('resUnit');

            if(isNaN(val)) { resEl.innerHTML = "Please enter a number"; return; }

            let result;
            if(cat === 'temp') {
                if(from === to) result = val;
                else if(from === 'Celsius' && to === 'Fahrenheit') result = (val * 9/5) + 32;
                else if(from === 'Celsius' && to === 'Kelvin') result = val + 273.15;
                else if(from === 'Fahrenheit' && to === 'Celsius') result = (val - 32) * 5/9;
                else if(from === 'Fahrenheit' && to === 'Kelvin') result = (val - 32) * 5/9 + 273.15;
                else if(from === 'Kelvin' && to === 'Celsius') result = val - 273.15;
                else if(from === 'Kelvin' && to === 'Fahrenheit') result = (val - 273.15) * 9/5 + 32;
            } else {
                // Base unit conversion
                const inBase = val / rates[cat][from];
                result = inBase * rates[cat][to];
            }
            
            resEl.innerHTML = `${val} ${from} = <span class="text-cyan-400">${Number(result.toFixed(4))}</span> ${to}`;
        }

        /* --- NEW: NUMBER SYSTEM LOGIC --- */
        function convertNumber(source) {
            const decInput = document.getElementById('numDec');
            const binInput = document.getElementById('numBin');
            const octInput = document.getElementById('numOct');
            const hexInput = document.getElementById('numHex');

            let decimalValue;

            try {
                if (source === 'dec') {
                    decimalValue = parseInt(decInput.value, 10);
                } else if (source === 'bin') {
                    decimalValue = parseInt(binInput.value, 2);
                } else if (source === 'oct') {
                    decimalValue = parseInt(octInput.value, 8);
                } else if (source === 'hex') {
                    decimalValue = parseInt(hexInput.value, 16);
                }

                if (isNaN(decimalValue)) {
                    if (source !== 'dec') decInput.value = "";
                    if (source !== 'bin') binInput.value = "";
                    if (source !== 'oct') octInput.value = "";
                    if (source !== 'hex') hexInput.value = "";
                    return;
                }

                if (source !== 'dec') decInput.value = decimalValue;
                if (source !== 'bin') binInput.value = decimalValue.toString(2);
                if (source !== 'oct') octInput.value = decimalValue.toString(8);
                if (source !== 'hex') hexInput.value = decimalValue.toString(16).toUpperCase();

            } catch (e) {
                console.error(e);
            }
        }

        /* --- NEW: MARKDOWN LOGIC --- */
        function renderMarkdown() {
            const input = document.getElementById('mdInput').value;
            const output = document.getElementById('mdOutput');
            output.innerHTML = marked.parse(input);
        }
        // Init Markdown placeholder
        document.getElementById('mdInput').value = "# Hello World\nStart typing **markdown** here...";
        renderMarkdown();

        /* --- SPEED TEST LOGIC --- */
        function runSpeedTest() {
            const pingRes = document.getElementById('pingResult');
            const dlRes = document.getElementById('dlResult');
            const status = document.getElementById('speedStatus');
            const btn = document.getElementById('btnSpeed');

            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> TESTING...';
            lucide.createIcons();
            
            pingRes.innerText = '--';
            dlRes.innerText = '--';
            status.innerText = 'Checking Latency...';

            // 1. Measure Ping (Latency)
            const startPing = Date.now();
            fetch(window.location.href + '?ping=' + startPing, { cache: 'no-store' })
                .then(() => {
                    const latency = Date.now() - startPing;
                    pingRes.innerText = latency;
                    status.innerText = 'Measuring Download Speed...';
                    
                    // 2. Measure Download Speed (Load a random image multiple times)
                    // Use a reliable, small public image or internal asset
                    const imgUrl = "https://upload.wikimedia.org/wikipedia/commons/3/3a/Cat03.jpg"; 
                    const downloadSize = 500000; // Approx 500KB (adjust based on actual image size)
                    const startTime = Date.now();
                    const download = new Image();
                    
                    download.onload = function () {
                        const endTime = Date.now();
                        const duration = (endTime - startTime) / 1000; // Seconds
                        const bitsLoaded = downloadSize * 8;
                        const speedBps = bitsLoaded / duration;
                        const speedMbps = (speedBps / 1024 / 1024).toFixed(2);
                        
                        dlRes.innerText = speedMbps;
                        status.innerText = 'Test Completed!';
                        btn.disabled = false;
                        btn.innerHTML = '<i data-lucide="play" class="w-5 h-5"></i> RESTART TEST';
                        lucide.createIcons();
                    };

                    download.onerror = function() {
                        status.innerText = "Error: Could not perform download test.";
                        btn.disabled = false;
                        btn.innerHTML = 'RETRY';
                    };

                    // Add cache buster to prevent cached download
                    download.src = imgUrl + "?n=" + Math.random();
                })
                .catch(err => {
                    status.innerText = "Error checking ping.";
                    btn.disabled = false;
                    btn.innerHTML = 'RETRY';
                });
        }
        
        /* --- WORLD CLOCK LOGIC --- */
        let worldClockInterval;
        let selectedTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone; // Default to local
        
        function initWorldMap() {
            // Simplified timezone map for key countries
            // In a real app, you'd use a more comprehensive library like moment-timezone or a geo-timezone API
            const countryTimezones = {
                'US': 'America/New_York', 'GB': 'Europe/London', 'CN': 'Asia/Shanghai', 
                'JP': 'Asia/Tokyo', 'AU': 'Australia/Sydney', 'IN': 'Asia/Kolkata', 
                'BR': 'America/Sao_Paulo', 'RU': 'Europe/Moscow', 'ZA': 'Africa/Johannesburg',
                'FR': 'Europe/Paris', 'DE': 'Europe/Berlin', 'ID': 'Asia/Jakarta', 'SG': 'Asia/Singapore',
                'CA': 'America/Toronto', 'MX': 'America/Mexico_City', 'KR': 'Asia/Seoul',
                'SA': 'Asia/Riyadh', 'TR': 'Europe/Istanbul', 'EG': 'Africa/Cairo'
            };

            const map = new jsVectorMap({
                selector: '#world-map',
                map: 'world',
                zoomButtons: true,
                zoomOnScroll: true,
                regionsSelectable: true,
                regionsSelectableOne: true,
                selectedRegions: [], // Initially select none or local country code if known
                bindPopup: function(code) { // Optional: Custom text on hover if needed, but default name works
                    return map.mapData.paths[code].name;
                },
                regionStyle: {
                    initial: { fill: 'rgba(255,255,255,0.2)', stroke: 'rgba(255,255,255,0.1)', strokeWidth: 0.5, fillOpacity: 1 },
                    hover: { fill: 'rgba(45, 212, 191, 0.5)' }, // Teal-400 hover
                    selected: { fill: '#2dd4bf' } // Teal-400 selected
                },
                onRegionClick: function (event, code) {
                    // FIX: Use 'this' context which binds to the map instance in callback
                    let countryName = code; 
                    if (this.mapData && this.mapData.paths && this.mapData.paths[code]) {
                        countryName = this.mapData.paths[code].name;
                    }
                    document.getElementById('selectedCountry').innerText = countryName;
                    
                    // Set Timezone
                    if(countryTimezones[code]) {
                        selectedTimezone = countryTimezones[code];
                    } else {
                        // Fallback: try to guess or inform user
                        selectedTimezone = 'UTC'; // Or use a library to look it up
                        document.getElementById('selectedCountry').innerText = countryName + " (Timezone Est: UTC)";
                    }
                    
                    // Remove manual selection calls to rely on regionsSelectable
                    // The library handles highlighting automatically
                    
                    updateClock();
                }
            });
            window.worldMapInstance = map;
            
            // Start the clock tick
            if(worldClockInterval) clearInterval(worldClockInterval);
            worldClockInterval = setInterval(updateClock, 1000);
            updateClock();
        }
        
        function updateClock() {
            try {
                const now = new Date();
                const options = { timeZone: selectedTimezone, hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
                const timeString = new Intl.DateTimeFormat('en-US', options).format(now);
                
                const dateOptions = { timeZone: selectedTimezone, weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateString = new Intl.DateTimeFormat('en-US', dateOptions).format(now);
                
                document.getElementById('digitalClock').innerText = timeString;
                document.getElementById('currentDate').innerText = dateString;
            } catch(e) {
                console.error("Invalid Timezone", e);
                document.getElementById('digitalClock').innerText = "--:--:--";
            }
        }

    </script>
    
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        @keyframes bounce-in {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounce-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
    </style>
</body>
</html>