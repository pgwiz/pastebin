<footer class="mt-auto py-8 border-t border-slate-800/50 bg-slate-900/50 backdrop-blur-md">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-slate-500 text-sm">
                &copy; <?= date('Y') ?> <span class="text-slate-300 font-semibold">MyPastebin</span>. All rights reserved.
            </div>
            
            <div class="flex items-center gap-6">
                <a href="#" class="text-slate-500 hover:text-blue-400 transition-colors"><i class="fab fa-twitter text-lg"></i></a>
                <a href="#" class="text-slate-500 hover:text-blue-600 transition-colors"><i class="fab fa-facebook text-lg"></i></a>
                <a href="#" class="text-slate-500 hover:text-gray-100 transition-colors"><i class="fab fa-github text-lg"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Admin Summary Modal -->
<div class="modal fade" id="adminSummaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-slate-900/95 backdrop-blur-xl border border-slate-700 shadow-2xl rounded-2xl">
            <div class="modal-header border-slate-700 bg-slate-800/50 rounded-t-2xl">
                <h5 class="modal-title text-white font-bold flex items-center gap-2">
                    <i class="fas fa-shield-alt text-blue-500"></i> Admin Summary
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <!-- Loader -->
                <div id="adminSummaryLoader" class="text-center py-8">
                     <div class="spinner-border text-blue-500" role="status"></div>
                </div>

                <!-- Content -->
                <div id="adminSummaryContent" class="hidden space-y-6">
                    <h3 id="asTitle" class="text-xl font-bold text-white mb-1"></h3>
                    <div class="flex gap-2 mb-4">
                        <span id="asFeatured" class="px-2 py-0.5 rounded text-xs"></span>
                        <span id="asViews" class="px-2 py-0.5 rounded bg-slate-700 text-slate-300 text-xs"></span>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-red-500/10 p-4 rounded-xl border border-red-500/20">
                            <p class="text-red-400 text-xs font-semibold uppercase">Reports</p>
                            <p id="asReportCount" class="text-2xl font-bold text-white"></p>
                        </div>
                         <div class="bg-blue-500/10 p-4 rounded-xl border border-blue-500/20">
                            <p class="text-blue-400 text-xs font-semibold uppercase">Shares</p>
                            <p id="asShareCount" class="text-2xl font-bold text-white"></p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-slate-400 text-sm font-semibold mb-2">Recent Reports:</p>
                            <div id="asReportList" class="space-y-2"></div>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm font-semibold mb-2">Shared With:</p>
                            <div id="asShareList" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
function openAdminSummary(id) {
    const modal = new bootstrap.Modal(document.getElementById('adminSummaryModal'));
    modal.show();

    // Reset logic
    document.getElementById('adminSummaryLoader').classList.remove('hidden');
    document.getElementById('adminSummaryContent').classList.add('hidden');
    document.getElementById('asReportList').innerHTML = '';
    document.getElementById('asShareList').innerHTML = '';

    fetch('get_admin_summary.php?id=' + id)
    .then(r => r.json())
    .then(data => {
        if(data.error) {
             alert(data.error); return;
        }

        // Populate
        document.getElementById('asTitle').innerText = data.title;
        document.getElementById('asViews').innerText = data.views + ' Views';
        
        const featEl = document.getElementById('asFeatured');
        if(data.is_featured) {
            featEl.innerText = 'Featured';
            featEl.className = 'px-2 py-0.5 rounded text-xs bg-green-500/20 text-green-400';
        } else {
            featEl.innerText = 'Standard';
             featEl.className = 'px-2 py-0.5 rounded text-xs bg-slate-600/30 text-slate-400';
        }

        document.getElementById('asReportCount').innerText = data.reports.count;
        document.getElementById('asShareCount').innerText = data.shares.count;

        // Reports List
        const repList = document.getElementById('asReportList');
        if(data.reports.recent.length === 0) {
            repList.innerHTML = '<span class="text-slate-600 text-sm italic">No recent reports</span>';
        } else {
            data.reports.recent.forEach(r => {
                const d = document.createElement('div');
                d.className = 'bg-slate-800/50 p-2 rounded text-sm text-red-300 border border-slate-700';
                d.innerText = r.reason;
                repList.appendChild(d);
            });
        }

        // Shares List
        const shList = document.getElementById('asShareList');
        if(data.shares.users.length === 0) {
            shList.innerHTML = '<span class="text-slate-600 text-sm italic">Not shared</span>';
        } else {
             data.shares.users.forEach(u => {
                const s = document.createElement('span');
                s.className = 'px-2 py-1 bg-blue-500/20 text-blue-300 rounded text-xs border border-blue-500/20';
                s.innerText = u;
                shList.appendChild(s);
            });
        }

        document.getElementById('adminSummaryLoader').classList.add('hidden');
        document.getElementById('adminSummaryContent').classList.remove('hidden');

    })
    .catch(e => {
        console.error(e);
        alert('Failed to load summary');
    });
}
</script>

</body>
</html>
