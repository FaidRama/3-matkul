<?php 
if(!defined('INDEX_LOADED')) { die("Direct access denied! Open index.php instead."); } 
?>
<!DOCTYPE html>
<html lang="en" class="dark"> <!-- Permanently Dark -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - utility𝕏press</title>
    
    <!-- Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/js/jsvectormap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class', // We keep this but will always force 'dark' class
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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="overflow-hidden text-white bg-[#0A0F1F] transition-colors duration-500">

    <!-- DYNAMIC BACKGROUND CONTAINER -->
    <div id="main-bg"></div>
    
    <!-- PARTICLES CONTAINER -->
    <div id="particles-js"></div>

    <div class="flex h-screen relative z-10">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-20 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300"></div>
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-72 -translate-x-full lg:translate-x-0 glass-panel border-r-0 m-4 mb-4 mt-4 mr-0 flex flex-col overflow-hidden shrink-0 shadow-2xl">
            <!-- UPDATED HEADER -->
            <div class="h-24 flex items-center px-6 border-b border-white/10 overflow-hidden whitespace-nowrap shrink-0">
                <a href="javascript:void(0)" onclick="location.reload()" class="flex items-center gap-3 transition-opacity duration-300 hover:opacity-80 group w-full">
                    <img src="logo.png" alt="Logo" class="h-12 w-auto object-contain shrink-0 transition-transform group-hover:scale-105">
                    <span class="logo-text text-lg font-heading font-bold tracking-wide text-white truncate">utility𝕏press</span>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <!-- MAIN -->
                <button onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active w-full flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-white/10 text-left text-sm group">
                    <div class="shrink-0"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></div>
                    <span class="sidebar-label font-medium">Dashboard</span>
                </button>

                <!-- CONVERSION TOOLS -->
                <div class="px-4 pt-4 pb-2 text-xs uppercase opacity-50 font-bold text-white category-header">Conversion Tools</div>
                
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
                <div class="px-4 pt-4 pb-2 text-xs uppercase opacity-50 font-bold text-white category-header">Utilities</div>

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
                        <p class="text-sm font-bold font-heading truncate text-white"><?= htmlspecialchars($_SESSION['username']) ?></p>
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
                    <button onclick="toggleSidebar()" class="p-2.5 rounded-xl hover:bg-white/10 transition-colors text-white">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h2 id="page-title" class="text-2xl font-heading font-extrabold tracking-tight drop-shadow-sm hidden md:block text-white">DASHBOARD</h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- GITHUB BUTTON -->
                    <a href="https://github.com/FaidRama/3-matkul" target="_blank" class="p-3 rounded-full hover:bg-white/20 transition-all active:scale-95 bg-white/5 border border-white/10 shadow-sm group text-white" title="View Source on GitHub">
                        <i data-lucide="github" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </a>
                    
                    <a href="?logout=true" class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white border border-white/20 px-6 py-2.5 rounded-full text-xs font-bold transition-all shadow-lg hover:shadow-red-500/30 flex items-center gap-2 font-heading tracking-wider active:scale-95">
                        <i data-lucide="log-out" class="w-3 h-3"></i> <span class="hidden sm:inline">LOGOUT</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 glass-panel overflow-y-auto p-6 lg:p-10 relative shadow-2xl">
                <div class="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
                    <?php include 'view_dashboard.php'; ?>
                    <?php include 'view_docx.php'; ?>
                    <?php include 'view_image.php'; ?>
                    <?php include 'view_qr.php'; ?>
                    <?php include 'view_media.php'; ?>
                    <?php include 'view_bmi.php'; ?>
                    <?php include 'view_unit.php'; ?>
                    <?php include 'view_number.php'; ?>
                    <?php include 'view_markdown.php'; ?>
                    <?php include 'view_speed.php'; ?>
                    <?php include 'view_worldclock.php'; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="dashboard.js"></script>
    
</body>
</html>