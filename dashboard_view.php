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
            transition: background 0.7s ease-in-out; /* Smooth Transition */
            background-size: 200% 200%;
            animation: gradientMove 15s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- LIQUID GLASS iOS 26 EFFECT --- */
        .glass-panel {
            /* Base Transparency */
            background: rgba(255, 255, 255, 0.05);
            
            /* The Blur & Saturation (Refraction Effect) */
            backdrop-filter: blur(30px) saturate(140%);
            -webkit-backdrop-filter: blur(30px) saturate(140%);
            
            /* The Border (Light Reflection) */
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.4);
            border-left: 1px solid rgba(255, 255, 255, 0.4);
            
            /* Deep Shadow & Inner Glow */
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1), /* Soft Drop Shadow */
                inset 0 0 20px rgba(255, 255, 255, 0.05); /* Inner Light */
            
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Dark Mode Specifics for Glass */
        .dark .glass-panel {
            background: rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.08);
            border-top-color: rgba(255, 255, 255, 0.15);
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        /* Light Mode Specifics */
        html:not(.dark) .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 
                0 20px 40px rgba(31, 38, 135, 0.15),
                inset 0 0 20px rgba(255, 255, 255, 0.5);
            color: #1e293b;
        }

        /* Inputs */
        .glass-input { 
            width: 100%; padding: 0.85rem 1.2rem; border-radius: 16px; 
            transition: all 0.2s; outline: none;
            backdrop-filter: blur(10px);
        }
        .dark .glass-input { 
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; 
        }
        .dark .glass-input:focus { border-color: rgba(255,255,255,0.5); background: rgba(0,0,0,0.5); box-shadow: 0 0 15px rgba(255,255,255,0.1); }
        
        html:not(.dark) .glass-input { 
            background: rgba(255,255,255,0.7); border: 1px solid rgba(0,0,0,0.05); color: #333; 
        }
        html:not(.dark) .glass-input:focus { border-color: #6366f1; background: white; box-shadow: 0 0 15px rgba(99, 102, 241, 0.2); }

        /* Typography */
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Sidebar Styling */
        #sidebar { transition: width 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.3s ease-in-out; }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.15); font-weight: 700;
            border-right: 3px solid white; /* Right indicator for modern look */
            border-radius: 12px;
        }
        html:not(.dark) .nav-item.active { background: rgba(0, 0, 0, 0.05); border-color: #333; }
        
        @media (min-width: 1024px) {
            #sidebar.collapsed { width: 5.5rem; }
            #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; margin: 0; pointer-events: none; }
            #sidebar.collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
            #sidebar.collapsed .logo-text, #sidebar.collapsed .user-info { display: none; }
        }
        .sidebar-label { transition: all 0.3s ease; white-space: nowrap; overflow: hidden; opacity: 1; margin-left: 0.75rem; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.5); border-radius: 20px; }
    </style>
</head>
<body class="overflow-hidden dark:text-white text-slate-800 transition-colors duration-500">

    <!-- DYNAMIC BACKGROUND CONTAINER -->
    <div id="main-bg"></div>

    <div class="flex h-screen relative z-10">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-20 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300"></div>
        
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-72 -translate-x-full lg:translate-x-0 glass-panel border-r-0 m-4 mb-4 mt-4 mr-0 flex flex-col overflow-hidden shrink-0 shadow-2xl">
            <div class="h-24 flex items-center px-6 border-b border-white/10 overflow-hidden whitespace-nowrap shrink-0">
                <!-- UPDATED LOGO: Visible in light mode (blue gradient) and dark mode (white glass) -->
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br dark:from-white/20 dark:to-white/5 from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-inner shrink-0 mr-3 border border-white/20">
                    <span class="font-heading">S</span>
                </div>
                <!-- UPDATED TEXT: Dark text in light mode, White in dark mode -->
                <span class="logo-text text-xl font-heading tracking-wide font-bold transition-opacity duration-300 dark:text-white text-slate-800">SecureTools</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Dashboard</span>
                </button>
                <button onclick="switchTab('docx')" id="nav-docx" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="file-text" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">DOCX to PDF</span>
                </button>
                <button onclick="switchTab('image')" id="nav-image" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="image" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Image Compress</span>
                </button>
                <button onclick="switchTab('qr')" id="nav-qr" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="qr-code" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">QR Generator</span>
                </button>
                <button onclick="switchTab('media')" id="nav-media" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="music" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">MP4 to MP3</span>
                </button>
                <button onclick="switchTab('bmi')" id="nav-bmi" class="nav-item w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="scale" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">BMI Calculator</span>
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
                    <!-- UPDATED HEADER: Visible in Light Mode -->
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
                            <!-- Title Color Updated for Light Mode (Midnight Blue) -->
                            <h3 class="text-4xl lg:text-5xl font-heading font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-950 to-blue-800 dark:from-white dark:to-gray-400">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
                            <p class="opacity-70 text-lg font-light dark:text-white text-blue-900">Your secure workspace is ready for action.</p>
                        </div>
                        
                        <div class="mt-8">
                            <div class="flex justify-between items-center mb-6">
                                <!-- Heading Color Updated -->
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
                                        <?php else: foreach($files as $base=>$pair): 
                                            $d=$pair['docx']??null; $p=$pair['pdf']??null;
                                            if(!$d && !$p) continue;
                                            $ts = explode('_',$base)[0]; 
                                            $date = is_numeric($ts) ? date("d M H:i", $ts) : "-";
                                        ?>
                                        <tr class="hover:bg-white/5 transition-colors">
                                            <td class="px-6 py-4 font-mono text-xs opacity-60"><?= $date ?></td>
                                            <td class="px-6 py-4 flex items-center gap-3 font-medium">
                                                <i data-lucide="file" class="w-4 h-4 text-blue-900 dark:text-blue-400"></i> <?= $d ? htmlspecialchars($d['filename']) : '-' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <!-- UPDATED BUTTON: Neon Green in Light Mode, Emerald in Dark Mode -->
                                                <?php if($p): ?><a href="download.php?id=<?= $p['id'] ?>" class="text-xs bg-lime-400 hover:bg-lime-500 text-black border border-lime-500 dark:bg-emerald-500/20 dark:hover:bg-emerald-500 dark:text-emerald-300 dark:hover:text-white dark:border-emerald-500/50 px-3 py-1.5 rounded-lg flex items-center gap-2 w-fit font-bold transition-all"><i data-lucide="download" class="w-3 h-3"></i> Download PDF</a><?php else: echo "<span class='opacity-50 italic'>Pending...</span>"; endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
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
                                    <!-- Centered Icon -->
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
                                    <!-- Centered Icon -->
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
                                    <!-- Centered Icon -->
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
                                        <!-- Centered Icon -->
                                        <i data-lucide="ruler" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-orange-400/70 group-hover:text-orange-400 transition-colors"></i>
                                    </div>
                                    <div class="relative group">
                                        <input type="number" id="bW" placeholder="Weight (kg)" class="glass-input pl-14 group-hover:border-orange-500/50 transition-colors">
                                        <!-- Centered Icon -->
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
                                    <!-- Centered Icon -->
                                    <i data-lucide="film" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-rose-400/70 group-hover:text-rose-400 transition-colors"></i>
                                </div>
                                <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold shadow-lg shadow-rose-900/20 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2 font-heading tracking-wide">
                                    <i data-lucide="music-2" class="w-5 h-5"></i> CONVERT TO MP3
                                </button>
                            </form>
                            <div id="resMedia" class="mt-8"></div>
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
        
        // Configuration for backgrounds
        const themes = {
            'dashboard': {
                // UPDATED: Blue 900 to Black (Dark) | Blue 200 to White (Light - Midnight Blue Hint)
                dark: 'linear-gradient(135deg, #1e3a8a 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #dbeafe 0%, #FFFFFF 100%)' 
            },
            'docx': {
                // Light Brown (Amber)
                dark: 'linear-gradient(135deg, #451a03 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #FEF3C7 0%, #FFFFFF 100%)'
            },
            'image': {
                // Green (Emerald)
                dark: 'linear-gradient(135deg, #064E3B 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #D1FAE5 0%, #FFFFFF 100%)'
            },
            'qr': {
                // Sky Blue
                dark: 'linear-gradient(135deg, #0C4A6E 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #E0F2FE 0%, #FFFFFF 100%)'
            },
            'media': {
                // Red (Rose)
                dark: 'linear-gradient(135deg, #881337 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #FFE4E6 0%, #FFFFFF 100%)'
            },
            'bmi': {
                // Orange
                dark: 'linear-gradient(135deg, #7C2D12 0%, #000000 100%)',
                light: 'linear-gradient(135deg, #FFEDD5 0%, #FFFFFF 100%)'
            }
        };

        let currentTab = 'dashboard';

        // Check LocalStorage
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Initial BG Set
        updateBackground();

        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            updateBackground(); // Update gradient when theme changes
        }

        function updateBackground() {
            const isDark = document.documentElement.classList.contains('dark');
            const gradient = isDark ? themes[currentTab].dark : themes[currentTab].light;
            bgEl.style.background = gradient;
        }

        // SIDEBAR LOGIC
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
            // Update Tab State
            currentTab = id;
            updateBackground(); // Trigger Color Change

            // View Switching
            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            const activeView = document.getElementById('view-'+id);
            activeView.classList.remove('hidden');
            
            // Add fade animation class again to trigger it
            activeView.classList.remove('animate-fade-in-up');
            void activeView.offsetWidth; // trigger reflow
            activeView.classList.add('animate-fade-in-up');

            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.getElementById('nav-'+id).classList.add('active');
            
            const titles = {'dashboard':'DASHBOARD','docx':'DOCX TO PDF','image':'IMAGE COMPRESS','qr':'QR GENERATOR','bmi':'BMI CALCULATOR', 'media':'MP4 TO MP3'};
            document.getElementById('page-title').innerText = titles[id];
            
            if(window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('sidebar-overlay').classList.add('hidden');
            }
        }

        // --- API HELPERS ---
        const API = "?endpoint=";
        const msg = (el, txt, err=false) => {
            const color = err ? 'red' : 'emerald';
            const icon = err ? 'alert-circle' : 'check-circle';
            el.innerHTML = `
                <div class="animate-bounce-in p-4 rounded-xl text-center font-bold bg-${color}-500/20 text-${color}-200 border border-${color}-500/30 flex flex-col items-center gap-2 shadow-lg backdrop-blur-md">
                    <i data-lucide="${icon}" class="w-8 h-8 text-${color}-400"></i>
                    <span>${txt}</span>
                </div>`;
            lucide.createIcons();
        };

        // DOCX
        document.getElementById('formDoc').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resDoc');
            const f=document.getElementById('fileDoc').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); 
            res.innerHTML = '<div class="text-center animate-pulse opacity-70">Uploading and Converting...</div>';
            try {
                const r=await fetch(API+'/doc/convert',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `<a href="${d.downloadUrl}" class="underline decoration-2 underline-offset-4 hover:text-white transition-colors">Success! Click here to Download PDF</a>`);
            } catch(e){ msg(res, e, true); }
        };

        // IMAGE
        document.getElementById('formImg').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resImg');
            const f=document.getElementById('fileImg').files[0]; if(!f) return;
            const fd=new FormData(); fd.append('file', f); 
            res.innerHTML = '<div class="text-center animate-pulse opacity-70">Compressing Image...</div>';
            try {
                const r=await fetch(API+'/image/compress',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `Saved ${((d.originalSize-d.compressedSize)/1024).toFixed(1)} KB! <a href="${d.downloadUrl}" class="underline ml-1">Download</a>`);
            } catch(e){ msg(res, e, true); }
        };

        // QR
        document.getElementById('formQr').onsubmit = async (e) => {
            e.preventDefault(); const res=document.getElementById('resQr'); 
            res.innerHTML = '<div class="text-center animate-pulse opacity-70">Generating QR Code...</div>';
            try {
                const r=await fetch(API+'/url/qr',{method:'POST',body:JSON.stringify({url:document.getElementById('inpUrl').value})}); const d=await r.json();
                if(d.error) throw d.error; 
                res.innerHTML=`
                    <div class="bg-white/10 p-6 rounded-2xl border border-white/20 inline-block shadow-2xl backdrop-blur-xl animate-fade-in-up">
                        <img src="${d.downloadUrl}" class="w-40 mx-auto rounded-lg mb-4 shadow-lg border border-white/10">
                        <a href="${d.downloadUrl}" download="${d.fileName}" class="bg-white text-slate-900 px-6 py-2 rounded-full text-xs font-bold flex items-center gap-2 justify-center w-full hover:bg-slate-200 transition-colors">
                            <i data-lucide="download" class="w-4 h-4"></i> SAVE IMAGE
                        </a>
                    </div>`;
                lucide.createIcons();
            } catch(e){ msg(res, e, true); }
        };

        // BMI
        document.getElementById('formBmi').onsubmit = async (e) => {
            e.preventDefault(); 
            const res = document.getElementById('resBmi'); 
            res.innerHTML = '<div class="text-center animate-pulse opacity-70">Calculating...</div>';
            
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
                <div class="bg-white/10 border border-white/20 rounded-2xl p-8 text-center shadow-2xl backdrop-blur-xl animate-fade-in-up">
                    <span class="text-sm uppercase tracking-widest opacity-60 mb-2 block">Your BMI Score</span>
                    <h4 class="text-6xl font-bold mb-2 font-heading tracking-tighter">${d.bmi}</h4>
                    <div class="inline-block px-4 py-1 rounded-full bg-white/10 border border-white/10 mb-6">
                        <p class="font-bold text-lg ${d.color}">${d.category}</p>
                    </div>
                    <div class="bg-black/20 rounded-xl p-4 text-sm opacity-90 border border-white/5 text-left flex gap-3">
                        <i data-lucide="lightbulb" class="w-5 h-5 shrink-0 text-yellow-300 mt-0.5"></i>
                        <span class="leading-relaxed">${d.message}</span>
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
            btn.disabled=true; btn.innerHTML=`<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Converting...`; btn.classList.add("opacity-50", "cursor-not-allowed");
            res.innerHTML=`<div class="p-4 rounded-xl text-center bg-blue-500/20 text-blue-200 border border-blue-500/30 animate-pulse font-bold">Processing Video...</div>`;
            try {
                const r=await fetch(API+'/media/convert',{method:'POST',body:fd}); const d=await r.json();
                if(d.error) throw d.error; msg(res, `✅ Success! <a href="${d.downloadUrl}" class="underline font-bold text-green-400">Download MP3</a>`);
            } catch(e) { msg(res, e, true); }
            finally { btn.disabled=false; btn.innerHTML=`<i data-lucide="music-2" class="w-5 h-5"></i> CONVERT TO MP3`; btn.classList.remove("opacity-50", "cursor-not-allowed"); lucide.createIcons(); }
        };
    </script>
    
    <style>
        /* Custom Animations */
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
            100% { transform: scale(1); opacitya: 1; }
        }
        .animate-bounce-in {
            animation: bounce-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
    </style>
</body>
</html>