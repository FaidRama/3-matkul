lucide.createIcons();

// --- DYNAMIC BACKGROUND LOGIC ---
const bgEl = document.getElementById('main-bg');

// Fixed Dark Themes
const themes = {
    'dashboard': 'linear-gradient(135deg, #1e3a8a 0%, #000000 100%)', // Blue 900
    'docx': 'linear-gradient(135deg, #451a03 0%, #000000 100%)',      // Amber
    'image': 'linear-gradient(135deg, #064E3B 0%, #000000 100%)',     // Emerald
    'qr': 'linear-gradient(135deg, #0C4A6E 0%, #000000 100%)',        // Sky
    'media': 'linear-gradient(135deg, #881337 0%, #000000 100%)',     // Rose
    'bmi': 'linear-gradient(135deg, #7C2D12 0%, #000000 100%)',       // Orange
    'unit': 'linear-gradient(135deg, #083344 0%, #000000 100%)',      // Cyan
    'number': 'linear-gradient(135deg, #2e1065 0%, #000000 100%)',    // Violet
    'markdown': 'linear-gradient(135deg, #701a75 0%, #000000 100%)',  // Fuchsia
    'speed': 'linear-gradient(135deg, #312e81 0%, #000000 100%)',     // Indigo
    'worldclock': 'linear-gradient(135deg, #115e59 0%, #000000 100%)' // Teal
};

let currentTab = 'dashboard';

// Enforce Dark Background Initial
updateBackground();

function updateBackground() {
    // Always use the theme defined, no light/dark check needed
    const gradient = themes[currentTab] || themes['dashboard'];
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

// BMI Logic
document.getElementById('formBmi').onsubmit = async (e) => { 
    e.preventDefault(); 
    const res = document.getElementById('resBmi'); 
    res.innerHTML = '<div class="text-center animate-pulse opacity-70">Calculating...</div>'; 
    try { 
        const r = await fetch(API+'/calc/bmi',{ method:'POST', body:JSON.stringify({ heightCm: document.getElementById('bH').value, weightKg: document.getElementById('bW').value }) }); 
        const d = await r.json(); 
        if(d.error) throw d.error; 
        res.innerHTML = `
        <div class="bg-white/10 border border-white/20 rounded-2xl p-8 text-center shadow-2xl backdrop-blur-xl animate-fade-in-up">
            <span class="text-sm uppercase tracking-widest opacity-60 mb-2 block text-white">Your BMI Score</span>
            <h4 class="text-6xl font-bold mb-2 font-heading tracking-tighter text-white">${d.bmi}</h4>
            <div class="inline-block px-4 py-1 rounded-full bg-white/10 border border-white/10 mb-6">
                <p class="font-bold text-lg ${d.color}">${d.category}</p>
            </div>
            <div class="bg-black/20 rounded-xl p-4 text-sm opacity-90 border border-white/5 text-left flex gap-3 text-white">
                <i data-lucide="lightbulb" class="w-5 h-5 shrink-0 text-yellow-300 mt-0.5"></i>
                <span class="leading-relaxed">${d.message}</span>
            </div>
        </div>`; 
        lucide.createIcons(); 
    } catch(e){ msg(res, "Error: " + e, true); } 
};

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