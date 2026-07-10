<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Doorprize — {{ $event->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#0a0a0f; color:#fff; height:100vh; overflow:hidden; display:flex; flex-direction:column; position:relative; }
        .bg-layer { position:absolute; inset:0; z-index:0; }
        .bg-layer img { width:100%; height:100%; object-fit:cover; opacity:.35; }
        .bg-overlay { position:absolute; inset:0; background:linear-gradient(180deg,rgba(10,10,15,.6) 0%,rgba(10,10,15,.85) 50%,rgba(10,10,15,.95) 100%); z-index:1; }
        .content { position:relative; z-index:2; display:flex; flex-direction:column; height:100vh; }

        /* Top bar */
        .top-bar { padding:24px 40px; display:flex; align-items:center; justify-content:space-between; }
        .top-bar .event-title { font-size:18px; font-weight:800; opacity:.9; display:flex; align-items:center; gap:10px; }
        .top-bar .event-title .material-symbols-outlined { font-size:24px; color:#fbbf24; }
        .selectors { display:flex; gap:12px; }
        .selectors select { background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); color:#fff; padding:10px 16px; border-radius:12px; font-size:13px; font-weight:600; font-family:inherit; cursor:pointer; min-width:180px; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
        .selectors select:focus { outline:none; border-color:rgba(99,102,241,.6); }

        /* Center stage */
        .stage { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:20px; padding:0 40px; }
        .prize-info { text-align:center; font-size:14px; font-weight:600; color:rgba(255,255,255,.5); }
        .prize-info .prize-name { font-size:20px; font-weight:800; color:#fbbf24; margin-bottom:4px; }

        /* Roller */
        .roller-container { width:100%; max-width:700px; height:340px; position:relative; overflow:hidden; border-radius:24px; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.06); }
        .roller-mask { position:absolute; inset:0; z-index:3; pointer-events:none; background:linear-gradient(180deg,rgba(10,10,15,1) 0%,transparent 30%,transparent 70%,rgba(10,10,15,1) 100%); }
        .roller-highlight { position:absolute; left:0; right:0; top:50%; transform:translateY(-50%); height:72px; border-top:2px solid rgba(99,102,241,.4); border-bottom:2px solid rgba(99,102,241,.4); background:rgba(99,102,241,.06); z-index:2; pointer-events:none; }
        .roller-track { position:absolute; left:0; right:0; top:0; transition:none; z-index:1; }
        .roller-item { height:72px; display:flex; align-items:center; justify-content:center; flex-direction:column; }
        .roller-item .rname { font-size:28px; font-weight:800; color:rgba(255,255,255,.3); transition:color .15s; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90%; }
        .roller-item .rorg { font-size:13px; font-weight:600; color:rgba(255,255,255,.12); }
        .roller-item.active .rname { color:#fff; }
        .roller-item.active .rorg { color:rgba(255,255,255,.4); }

        /* Winner reveal */
        .winner-reveal { display:none; flex-direction:column; align-items:center; text-align:center; }
        .winner-reveal.visible { display:flex; animation:winnerPop .6s cubic-bezier(.175,.885,.32,1.275); }
        .winner-reveal .trophy { font-size:64px; color:#fbbf24; margin-bottom:12px; filter:drop-shadow(0 0 30px rgba(251,191,36,.4)); }
        .winner-reveal .wname { font-size:52px; font-weight:900; background:linear-gradient(135deg,#fbbf24,#f59e0b,#fcd34d); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1.2; }
        .winner-reveal .worg { font-size:20px; font-weight:600; color:rgba(255,255,255,.6); margin-top:8px; }
        .winner-reveal .wprize { font-size:14px; font-weight:700; color:rgba(99,102,241,.8); margin-top:16px; padding:8px 20px; border-radius:100px; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2); }
        @keyframes winnerPop { 0%{transform:scale(.5);opacity:0} 100%{transform:scale(1);opacity:1} }

        /* Idle state */
        .idle-msg { text-align:center; color:rgba(255,255,255,.3); font-size:16px; font-weight:600; }
        .idle-msg .material-symbols-outlined { font-size:48px; display:block; margin-bottom:8px; opacity:.4; }
        .pulse { animation:pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:.3} 50%{opacity:.6} }

        /* Bottom bar */
        .bottom-bar { padding:20px 40px 28px; display:flex; align-items:center; justify-content:space-between; }
        .recent-winners { display:flex; align-items:center; gap:8px; flex-wrap:wrap; max-width:65%; }
        .recent-winners .label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,.35); margin-right:4px; }
        .winner-chip { padding:6px 14px; border-radius:100px; background:rgba(251,191,36,.08); border:1px solid rgba(251,191,36,.15); font-size:12px; font-weight:700; color:#fbbf24; display:flex; align-items:center; gap:5px; }
        .winner-chip .material-symbols-outlined { font-size:14px; }
        .eligible-count { font-size:12px; font-weight:600; color:rgba(255,255,255,.3); }

        /* Draw button */
        .draw-btn { padding:16px 48px; border-radius:16px; font-size:16px; font-weight:800; font-family:inherit; cursor:pointer; border:none; transition:all .2s; text-transform:uppercase; letter-spacing:1px; display:flex; align-items:center; gap:10px; }
        .draw-btn.start { background:linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; box-shadow:0 8px 32px rgba(99,102,241,.3); }
        .draw-btn.start:hover { transform:translateY(-2px); box-shadow:0 12px 40px rgba(99,102,241,.4); }
        .draw-btn.stop { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; box-shadow:0 8px 32px rgba(239,68,68,.3); }
        .draw-btn.stop:hover { transform:translateY(-2px); box-shadow:0 12px 40px rgba(239,68,68,.4); }
        .draw-btn:disabled { opacity:.4; cursor:not-allowed; transform:none !important; box-shadow:none !important; }
        .draw-btn .countdown { font-variant-numeric:tabular-nums; }

        /* No-slots badge */
        .no-slots { padding:12px 24px; border-radius:12px; background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.2); color:#f87171; font-size:14px; font-weight:700; }
    </style>
</head>
<body>
    {{-- Background --}}
    <div class="bg-layer">
        @if($event->doorprize_background)
            <img src="{{ asset('storage/' . $event->doorprize_background) }}" alt="Background"/>
        @endif
    </div>
    <div class="bg-overlay"></div>

    <div class="content">
        {{-- Top Bar --}}
        <div class="top-bar">
            <div class="event-title">
                <span class="material-symbols-outlined">redeem</span>
                {{ $event->title }} — Doorprize
            </div>
            <div class="selectors">
                <select id="sessionSelect" onchange="onSessionChange()">
                    <option value="">— Select Session —</option>
                </select>
                <select id="prizeSelect" onchange="onPrizeChange()">
                    <option value="">— Select Prize —</option>
                </select>
            </div>
        </div>

        {{-- Center Stage --}}
        <div class="stage">
            <div id="prizeInfo" class="prize-info" style="display:none">
                <div class="prize-name" id="prizeNameDisplay"></div>
                <div>Remaining: <span id="prizeRemaining"></span> / <span id="prizeTotal"></span></div>
            </div>

            {{-- Idle --}}
            <div id="idleState" class="idle-msg pulse">
                <span class="material-symbols-outlined">casino</span>
                Select a session and prize to begin
            </div>

            {{-- Roller --}}
            <div id="rollerContainer" class="roller-container" style="display:none">
                <div class="roller-mask"></div>
                <div class="roller-highlight"></div>
                <div class="roller-track" id="rollerTrack"></div>
            </div>

            {{-- Winner Reveal --}}
            <div id="winnerReveal" class="winner-reveal">
                <span class="material-symbols-outlined trophy">emoji_events</span>
                <div class="wname" id="winnerName"></div>
                <div class="worg" id="winnerOrg"></div>
                <div class="wprize" id="winnerPrize"></div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="bottom-bar">
            <div>
                <div class="recent-winners" id="recentWinners">
                    <span class="label">Recent Winners:</span>
                    <span style="font-size:12px;color:rgba(255,255,255,.25);font-weight:500">No winners yet</span>
                </div>
                <div class="eligible-count" id="eligibleCount" style="margin-top:6px"></div>
            </div>
            <div id="btnArea">
                <button class="draw-btn start" id="drawBtn" disabled onclick="handleDrawBtn()">
                    <span class="material-symbols-outlined">casino</span>
                    <span id="btnLabel">Start</span>
                </button>
            </div>
        </div>
    </div>

<script>
// ─── Data ───
const DRAW_URL = @json(route('events.doorprize.draw', $event->slug));
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let sessions = {!! $sessionsJson !!};
let eligibleNames = {!! $eligibleNamesJson !!};

let selectedSessionId = null;
let selectedPrizeId = null;
let currentPrize = null;
let currentSession = null;

// ─── States ───
let state = 'idle'; // idle | rolling | stopping | revealed | cooldown
let rollerAnim = null;
let rollerPos = 0;
let rollerSpeed = 0;
const ITEM_H = 72;
let cooldownTimer = null;
let recentWinnersList = [];

// ─── Init ───
(function init() {
    const sel = document.getElementById('sessionSelect');
    sessions.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.name;
        sel.appendChild(opt);
    });
    updateEligibleCount();

    // Collect existing winners
    sessions.forEach(s => {
        s.prizes.forEach(p => {
            p.winners.forEach(w => {
                recentWinnersList.push({ name: w.name, prize: p.name });
            });
        });
    });
    renderRecentWinners();
})();

function onSessionChange() {
    const sel = document.getElementById('sessionSelect');
    selectedSessionId = sel.value ? parseInt(sel.value) : null;
    currentSession = sessions.find(s => s.id === selectedSessionId) || null;

    const pSel = document.getElementById('prizeSelect');
    pSel.innerHTML = '<option value="">— Select Prize —</option>';
    if (currentSession) {
        currentSession.prizes.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name + (p.remaining <= 0 ? ' (FULL)' : ` (${p.remaining} left)`);
            if (p.remaining <= 0) opt.disabled = true;
            pSel.appendChild(opt);
        });
    }
    selectedPrizeId = null;
    currentPrize = null;
    updateUI();
}

function onPrizeChange() {
    const sel = document.getElementById('prizeSelect');
    selectedPrizeId = sel.value ? parseInt(sel.value) : null;
    currentPrize = currentSession?.prizes.find(p => p.id === selectedPrizeId) || null;
    updateUI();
}

function updateUI() {
    const idle = document.getElementById('idleState');
    const roller = document.getElementById('rollerContainer');
    const reveal = document.getElementById('winnerReveal');
    const btn = document.getElementById('drawBtn');
    const info = document.getElementById('prizeInfo');

    if (!currentPrize || !currentSession) {
        idle.style.display = '';
        roller.style.display = 'none';
        reveal.classList.remove('visible');
        info.style.display = 'none';
        btn.disabled = true;
        state = 'idle';
        return;
    }

    document.getElementById('prizeNameDisplay').textContent = currentPrize.name;
    document.getElementById('prizeRemaining').textContent = currentPrize.remaining;
    document.getElementById('prizeTotal').textContent = currentPrize.max_winners;
    info.style.display = '';

    if (currentPrize.remaining <= 0) {
        idle.style.display = 'none';
        roller.style.display = 'none';
        reveal.classList.remove('visible');
        btn.disabled = true;
        document.getElementById('btnLabel').textContent = 'No Slots';
    } else {
        idle.style.display = 'none';
        roller.style.display = '';
        reveal.classList.remove('visible');
        btn.disabled = false;
        btn.className = 'draw-btn start';
        document.getElementById('btnLabel').textContent = 'Start';
        state = 'ready';
        buildRoller();
    }
    updateEligibleCount();
}

function updateEligibleCount() {
    document.getElementById('eligibleCount').textContent =
        `Eligible pool: ${eligibleNames.length} participants`;
}

// ─── Roller ───
function buildRoller() {
    const track = document.getElementById('rollerTrack');
    track.innerHTML = '';
    if (eligibleNames.length === 0) return;

    // Build enough items to fill viewport + buffer (repeat names)
    const needed = Math.max(80, eligibleNames.length * 3);
    for (let i = 0; i < needed; i++) {
        const data = eligibleNames[i % eligibleNames.length];
        const div = document.createElement('div');
        div.className = 'roller-item';
        div.innerHTML = `<div class="rname">${escHtml(data.name)}</div><div class="rorg">${escHtml(data.organization)}</div>`;
        track.appendChild(div);
    }
    rollerPos = 0;
    track.style.transform = `translateY(0px)`;
    highlightCenter();
}

function highlightCenter() {
    const track = document.getElementById('rollerTrack');
    const items = track.children;
    const containerH = 340;
    const centerY = containerH / 2;
    for (let i = 0; i < items.length; i++) {
        const itemTop = i * ITEM_H + rollerPos;
        const itemCenter = itemTop + ITEM_H / 2;
        if (Math.abs(itemCenter - centerY) < ITEM_H / 2) {
            items[i].classList.add('active');
        } else {
            items[i].classList.remove('active');
        }
    }
}

// ─── Draw Button ───
function handleDrawBtn() {
    if (state === 'ready' || state === 'idle') {
        startRolling();
    } else if (state === 'rolling') {
        stopRolling();
    }
}

function startRolling() {
    state = 'rolling';
    const btn = document.getElementById('drawBtn');
    btn.className = 'draw-btn stop';
    document.getElementById('btnLabel').textContent = 'Stop';

    document.getElementById('winnerReveal').classList.remove('visible');
    document.getElementById('rollerContainer').style.display = '';

    rollerSpeed = 18; // pixels per frame
    rollerPos = 0;

    function animate() {
        if (state !== 'rolling' && state !== 'stopping') return;
        const track = document.getElementById('rollerTrack');
        const totalH = track.children.length * ITEM_H;

        rollerPos -= rollerSpeed;
        if (Math.abs(rollerPos) >= totalH / 2) rollerPos = 0; // loop

        track.style.transform = `translateY(${rollerPos}px)`;
        highlightCenter();
        rollerAnim = requestAnimationFrame(animate);
    }
    rollerAnim = requestAnimationFrame(animate);
}

async function stopRolling() {
    state = 'stopping';
    const btn = document.getElementById('drawBtn');
    btn.disabled = true;
    document.getElementById('btnLabel').textContent = '...';

    // POST to server to pick winner
    let result;
    try {
        const resp = await fetch(DRAW_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ session_id: selectedSessionId, prize_id: selectedPrizeId }),
        });
        result = await resp.json();
        if (result.error) { alert(result.error); resetToReady(); return; }
    } catch (e) { alert('Network error'); resetToReady(); return; }

    // Decelerate animation
    await decelerate(result.winner.name);

    // Update data
    eligibleNames = result.eligibleNames;
    if (currentPrize) {
        currentPrize.remaining = result.prize.remaining;
        currentPrize.winners_count = result.prize.winners_count;
        document.getElementById('prizeRemaining').textContent = result.prize.remaining;
    }

    // Show winner
    cancelAnimationFrame(rollerAnim);
    document.getElementById('rollerContainer').style.display = 'none';
    showWinner(result.winner, result.prize.name);

    recentWinnersList.unshift({ name: result.winner.name, prize: result.prize.name });
    renderRecentWinners();
    updateEligibleCount();

    // Cooldown
    state = 'revealed';
    startCooldown(5);
}

function decelerate(winnerName) {
    return new Promise(resolve => {
        let speed = rollerSpeed;
        const decayRate = 0.96;
        const track = document.getElementById('rollerTrack');
        const totalH = track.children.length * ITEM_H;

        function frame() {
            speed *= decayRate;
            rollerPos -= speed;
            if (Math.abs(rollerPos) >= totalH / 2) rollerPos = 0;
            track.style.transform = `translateY(${rollerPos}px)`;
            highlightCenter();

            if (speed > 0.5) {
                requestAnimationFrame(frame);
            } else {
                resolve();
            }
        }
        requestAnimationFrame(frame);
    });
}

function showWinner(winner, prizeName) {
    state = 'revealed';
    document.getElementById('winnerName').textContent = winner.name;
    document.getElementById('winnerOrg').textContent = winner.organization || '';
    document.getElementById('winnerPrize').textContent = '🎁 ' + prizeName;
    document.getElementById('winnerReveal').classList.add('visible');

    // Confetti
    const count = 200;
    const defaults = { origin: { y: 0.6 }, zIndex: 9999 };
    function fire(particleRatio, opts) {
        confetti({ ...defaults, particleCount: Math.floor(count * particleRatio), ...opts });
    }
    fire(0.25, { spread: 26, startVelocity: 55 });
    fire(0.2, { spread: 60 });
    fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
    fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
    fire(0.1, { spread: 120, startVelocity: 45 });
}

function startCooldown(seconds) {
    let remaining = seconds;
    const btn = document.getElementById('drawBtn');
    btn.className = 'draw-btn start';
    btn.disabled = true;
    document.getElementById('btnLabel').innerHTML = `<span class="countdown">${remaining}s</span>`;

    cooldownTimer = setInterval(() => {
        remaining--;
        if (remaining <= 0) {
            clearInterval(cooldownTimer);
            resetToReady();
        } else {
            document.getElementById('btnLabel').innerHTML = `<span class="countdown">${remaining}s</span>`;
        }
    }, 1000);
}

function resetToReady() {
    cancelAnimationFrame(rollerAnim);
    const btn = document.getElementById('drawBtn');

    if (currentPrize && currentPrize.remaining <= 0) {
        btn.disabled = true;
        btn.className = 'draw-btn start';
        document.getElementById('btnLabel').textContent = 'No Slots';
        state = 'idle';
        document.getElementById('rollerContainer').style.display = 'none';
        return;
    }

    btn.disabled = false;
    btn.className = 'draw-btn start';
    document.getElementById('btnLabel').textContent = 'Start';
    state = 'ready';

    document.getElementById('winnerReveal').classList.remove('visible');
    document.getElementById('rollerContainer').style.display = '';
    buildRoller();
}

function renderRecentWinners() {
    const el = document.getElementById('recentWinners');
    if (recentWinnersList.length === 0) {
        el.innerHTML = '<span class="label">Recent Winners:</span><span style="font-size:12px;color:rgba(255,255,255,.25);font-weight:500">No winners yet</span>';
        return;
    }
    let html = '<span class="label">Recent Winners:</span>';
    recentWinnersList.slice(0, 6).forEach(w => {
        html += `<div class="winner-chip"><span class="material-symbols-outlined">trophy</span>${escHtml(w.name)}</div>`;
    });
    if (recentWinnersList.length > 6) html += `<span style="font-size:11px;color:rgba(255,255,255,.3)">+${recentWinnersList.length - 6} more</span>`;
    el.innerHTML = html;
}

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
</script>
</body>
</html>
