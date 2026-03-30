<div class="relative min-h-screen overflow-hidden bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-900/95 to-slate-950 px-4 py-10 text-slate-100 sm:px-6 md:px-10">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-20 top-10 h-72 w-72 rounded-full bg-cyan-500/30 blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-blue-500/20 blur-[140px]"></div>
    </div>

    <div class="relative mx-auto max-w-6xl space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-200/80">Financial Transaction System</p>
                <h1 class="mt-3 text-3xl font-bold leading-tight text-white sm:text-4xl">Secure Plaid Sandbox Connectivity</h1>
                <p class="mt-3 max-w-2xl text-sm text-slate-300">Instantly issue link tokens, guide testers through Plaid Link, and watch every webhook-grade detail stream into the debug console.</p>
            </div>
            <span class="rounded-full border border-cyan-200/50 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-cyan-100">Beta</span>
        </div>

        <div class="@container/hero grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
            <section class="rounded-[28px] border border-white/10 bg-gradient-to-br from-white/5 via-white/0 to-cyan-500/10 p-8 shadow-2xl shadow-cyan-900/40">
                <div class="flex flex-col gap-6">
                    <div class="space-y-3">
                        <p class="text-sm font-semibold text-cyan-200/90">Connect once, test endlessly</p>
                        <p class="text-sm text-slate-300">The sandbox toolkit provisions a link token, walks your QA team through Plaid Link, and securely exchanges the public token with Sanctum-authenticated APIs.</p>
                    </div>

                    <div class="grid gap-4 @md/hero:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-widest text-cyan-100">Quick Actions</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-200">
                                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Generate link token</li>
                                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Launch Plaid Link modal</li>
                                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>Exchange public token</li>
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-widest text-cyan-100">Sandbox Insights</p>
                            <dl class="mt-4 grid gap-4 text-sm">
                                <div>
                                    <dt class="text-slate-400">Connected test accounts</dt>
                                    <dd class="text-2xl font-semibold text-white">04</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400">Last sync</dt>
                                    <dd>2 min ago • Plaid Sandbox</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button id="connect-bank" type="button" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-800/40 transition hover:scale-[1.01] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-cyan-200/60">
                            Connect bank with Plaid
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-cyan-200/70 hover:text-white">Sandbox Guide</button>
                        <span class="text-xs text-slate-400">Version 1.0.0</span>
                    </div>
                </div>
            </section>

            <section class="rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-900/60">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-300">Checklist</p>
                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-900/40 p-4">
                        <p class="text-sm font-semibold text-white">1. Link token ready</p>
                        <p class="mt-1 text-xs text-slate-400">Issued from <code class="rounded bg-white/10 px-1">/api/v1/plaid/link-token</code></p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/40 p-4">
                        <p class="text-sm font-semibold text-white">2. Launch Plaid Link</p>
                        <p class="mt-1 text-xs text-slate-400">Plaid Link modal guides testers through sandbox login.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-4">
                        <p class="text-sm font-semibold text-emerald-100">3. Exchange token</p>
                        <p class="mt-1 text-xs text-emerald-200/80">Protected by Sanctum bearer authentication.</p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-gradient-to-br from-cyan-500/20 to-transparent p-4">
                    <p class="text-xs uppercase tracking-[0.35em] text-cyan-100">Live Status</p>
                    <p id="status" class="mt-3 text-sm text-slate-100">Ready. Click connect to begin.</p>
                </div>
            </section>
        </div>

        <div class="@container/metrics grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-300">Sandbox Accounts</p>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="flex items-center justify-between">
                        <span class="text-slate-200">Plaid Checking</span>
                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs">Active</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-slate-200">Plaid Savings</span>
                        <span class="rounded-full bg-white/5 px-2 py-0.5 text-xs">Ready</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-slate-200">Plaid Credit</span>
                        <span class="rounded-full bg-white/5 px-2 py-0.5 text-xs">Ready</span>
                    </li>
                </ul>
            </section>
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-300">Security snapshot</p>
                <div class="mt-4 space-y-3 text-sm text-slate-200">
                    <p class="flex items-center justify-between"><span>Sanctum token</span><span class="font-semibold text-emerald-300">Required</span></p>
                    <p class="flex items-center justify-between"><span>HTTPS</span><span class="font-semibold">Enforced</span></p>
                    <p class="flex items-center justify-between"><span>PII storage</span><span class="font-semibold">Mock only</span></p>
                </div>
            </section>
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-300">Sync health</p>
                <div class="mt-6 space-y-3 text-sm">
                    <div>
                        <p class="text-slate-300">Token freshness</p>
                        <div class="mt-2 h-2 rounded-full bg-white/10">
                            <div class="h-full w-5/6 rounded-full bg-gradient-to-r from-emerald-300 to-cyan-300"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-slate-300">Webhook coverage</p>
                        <div class="mt-2 h-2 rounded-full bg-white/10">
                            <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-cyan-300 to-blue-400"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="@container/console grid gap-6 lg:grid-cols-2">
            <section class="rounded-[28px] border border-white/10 bg-slate-900/40 p-6 shadow-lg shadow-slate-950/40">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-300">Runbook</p>
                <ol class="mt-4 space-y-3 text-sm text-slate-200">
                    <li class="flex gap-3">
                        <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/10 text-xs">1</span>
                        Request a link token via the authenticated endpoint.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/10 text-xs">2</span>
                        Launch Plaid Link with the returned token.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/10 text-xs">3</span>
                        Exchange the public token for permanent access using the Sanctum bearer token shown below.
                    </li>
                </ol>
                <p class="mt-5 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs text-slate-200">Tip: replace <code class="rounded bg-black/30 px-1.5 py-0.5">SANCTUM_TOKEN</code> with your real token from <code class="font-mono">php artisan tinker</code>.</p>
            </section>

            <section class="rounded-[28px] border border-white/10 bg-black/30 p-6 shadow-inner shadow-black/60">
                <div class="flex items-center justify-between text-xs uppercase tracking-[0.4em] text-slate-400">
                    <span>Debug console</span>
                    <span>Live</span>
                </div>
                <pre id="debug" class="mt-4 max-h-48 overflow-auto rounded-2xl border border-white/5 bg-black/50 p-4 text-[11px] leading-relaxed text-teal-200/80"></pre>
            </section>
        </div>
    </div>
</div>

<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script>
// Replace with the actual Sanctum token created for your user (e.g. via tinker or login).
const SANCTUM_TOKEN = 'YOUR_SANCTUM_TOKEN_HERE';
const statusEl = document.getElementById('status');
const debugEl = document.getElementById('debug');
const connectBtn = document.getElementById('connect-bank');

function setStatus(text, type = 'info') {
    statusEl.textContent = text;
    statusEl.className = `mt-3 text-sm ${
        type === 'error'
            ? 'text-red-200'
            : type === 'success'
                ? 'text-emerald-200'
                : 'text-slate-100'
    }`;
}

function appendDebug(message) {
    const suffix = `\n${new Date().toISOString()} - ${message}`;
    debugEl.textContent = (debugEl.textContent || '') + suffix;
}

connectBtn.addEventListener('click', async () => {
    try {
        connectBtn.disabled = true;
        connectBtn.classList.add('cursor-wait', 'opacity-75');
        setStatus('Requesting link token...');

        const res = await fetch('/api/v1/plaid/link-token', {
            headers: { 'Authorization': `Bearer 5|A4ILw6AB3g8XNYbVy2O4Rg9RpUXvbyhuGZSQt4WH7459424d` },
        });

        if (!res.ok) {
            throw new Error(`Link token request failed with status ${res.status}`);
        }

        const data = await res.json();
        appendDebug(`link-token response: ${JSON.stringify(data)}`);

        const linkToken = data?.data?.link_token ?? data?.link_token;
        if (!linkToken) {
            throw new Error('No link_token returned from server.');
        }

        setStatus('Opening Plaid Link...');

        const handler = Plaid.create({
            token: linkToken,
            onSuccess: async (public_token, metadata) => {
                setStatus('Public token received, exchanging for access token...');
                appendDebug(`Plaid success metadata: ${JSON.stringify(metadata)}`);

                const exchangeRes = await fetch('/api/v1/plaid/public-token/exchange', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${SANCTUM_TOKEN}`,
                    },
                    body: JSON.stringify({ public_token }),
                });

                if (!exchangeRes.ok) {
                    throw new Error(`Public token exchange failed with status ${exchangeRes.status}`);
                }

                const exchangeData = await exchangeRes.json();
                appendDebug(`exchange response: ${JSON.stringify(exchangeData)}`);
                setStatus('✅ Bank connected successfully!', 'success');
            },
            onExit: (err, metadata) => {
                if (err) {
                    setStatus('Plaid Link exited with error.', 'error');
                    appendDebug(`Plaid error: ${JSON.stringify(err)} metadata: ${JSON.stringify(metadata)}`);
                    return;
                }
                setStatus('Plaid Link closed.');
                appendDebug(`Plaid exit metadata: ${JSON.stringify(metadata)}`);
            },
        });

        handler.open();
    } catch (error) {
        console.error(error);
        setStatus(`Error: ${error.message}`, 'error');
        appendDebug(`Error: ${error.message}`);
    } finally {
        connectBtn.disabled = false;
        connectBtn.classList.remove('cursor-wait', 'opacity-75');
    }
});
</script>
