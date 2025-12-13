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