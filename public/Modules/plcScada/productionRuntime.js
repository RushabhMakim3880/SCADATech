/**
    * CycleMonitor v1.3 (globals)
    * - Title shows CURRENT STATE + live timer since state start.
    * - Totals show cumulative AND "recent (since last state change)" timers.
    * - Mirrors recent into programLogic.machineState.recent.{operative, paused, idle}
    * - Binds to global programLogic / PlcDataLayer (not window.* required if you exposed them).
    */
(function (w) {
    const DEFAULTS = {
        debounceMs: 200,
        pauseThresholdSec: 45,
        idleThresholdSec: 20,
        pauseReasons: [
            { id: 'TOOL_CHANGE', label: 'Tool Change' },
            { id: 'MATERIAL_FEED', label: 'Material Feed' },
            { id: 'QUALITY_CHECK', label: 'Quality Check' },
            { id: 'OPERATOR_BREAK', label: 'Operator Break' },
        ],
        idleReasons: [
            { id: 'NO_JOB', label: 'No Job' },
            { id: 'NO_OPERATOR', label: 'No Operator' },
            { id: 'MAINTENANCE', label: 'Maintenance' },
            { id: 'POWER_SAVE', label: 'Power Save' },
        ],
        tags: { AUTO_RUNNING: 611, AUTO_PAUSE: 613, MACHINE_IDLE: 614 }
    };

    // --- IST time helpers ---
    function nowISTParts() {
        const fmt = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Kolkata', year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
        });
        const parts = Object.fromEntries(fmt.formatToParts(new Date()).map(p => [p.type, p.value]));
        return { Y: parts.year, M: parts.month, D: parts.day, h: parts.hour, m: parts.minute, s: parts.second };
    }
    function nowMysqlIST() { const p = nowISTParts(); return `${p.Y}-${p.M}-${p.D} ${p.h}:${p.m}:${p.s}`; }
    function secToHMS(sec) {
        sec = Math.max(0, Math.floor(sec)); const h = Math.floor(sec / 3600), m = Math.floor((sec % 3600) / 60), s = sec % 60;
        const pad = n => String(n).padStart(2, '0'); return `${pad(h)}:${pad(m)}:${pad(s)}`;
    }

    // --- Internal store (references globals) ---
    const Store = {
        cfg: { ...DEFAULTS },
        programLogic: null,
        PlcDataLayer: null,
        tags: { 611: 0, 613: 0, 614: 0 },
        handlers: { 611: null, 613: null, 614: null },

        state: {
            current: 'INIT',
            sinceMysql: null,
            lastChangeAtMysql: null,
            lastDebounceTimer: null,
            lastCycle: null,
            pendingReason: null // {type:'PAUSE'|'IDLE', idx, openedAtMysql}
        },

        data: {
            totals: { operativeSec: 0, idleSec: 0, pausedSec: 0 },
            // "recent" = since last state change
            recent: { operativeSec: 0, idleSec: 0, pausedSec: 0 },
            cycles: [], pauses: [], idles: [],
            lastTickAt: Date.now()
        },

        logs: [],
        pushLog(msg) {
            const time = nowMysqlIST();
            this.logs.unshift({ time, msg }); if (this.logs.length > 500) this.logs.pop();
            DebugPanel.refreshLogs();
        }
    };

    // --- Debug Panel (stacked, logs bottom, newest-first) ---
    const DebugPanel = {
        isReady: false, isVisible: false, isMinimized: false, isFullscreen: false,
        ensure() {
            if (this.isReady) return;
            const html = `
    <div id="cm-debug" class="cm-debug shadow-lg" style="display:none;">
        <div class="cm-head d-flex justify-content-between align-items-center p-2">
            <strong>
                Cycle Monitor · <span id="cm-title-state">INIT</span> ·
                <span id="cm-title-timer">00:00:00</span>
            </strong>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-1" data-cm="minimize">_</button>
                <button class="btn btn-sm btn-outline-secondary me-1" data-cm="fullscreen">[ ]</button>
                <button class="btn btn-sm btn-outline-danger" data-cm="close">×</button>
            </div>
        </div>
        <div class="cm-body p-2">
            <div class="row g-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-1"><small>Live</small></div>
                        <div class="card-body py-2">
                            <div class="small mb-2">
                                <div>611 (AUTO RUNNING): <span id="cm-t-611">-</span></div>
                                <div>613 (AUTO PAUSE): <span id="cm-t-613">-</span></div>
                                <div>614 (MACHINE IDLE): <span id="cm-t-614">-</span></div>
                            </div>
                            <hr class="my-2">
                                <div class="small mb-2">
                                    <div>State: <span id="cm-state">INIT</span></div>
                                    <div>Since: <span id="cm-since">-</span></div>
                                    <div>Last Change: <span id="cm-last">-</span></div>
                                </div>
                                <hr class="my-2">
                                    <div class="small">
                                        <div><strong>Totals (HH:MM:SS)</strong></div>
                                        <div>Operative: <span id="cm-op">00:00:00</span> <small class="text-muted">(recent <span id="cm-op-recent">00:00:00</span>)</small></div>
                                        <div>Paused: <span id="cm-pause">00:00:00</span> <small class="text-muted">(recent <span id="cm-pause-recent">00:00:00</span>)</small></div>
                                        <div>Idle: <span id="cm-idle">00:00:00</span> <small class="text-muted">(recent <span id="cm-idle-recent">00:00:00</span>)</small></div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header py-1"><small>Event Log (newest first)</small></div>
                            <div class="card-body p-0">
                                <div id="cm-logs" class="small p-2" style="height:260px; overflow:auto; white-space:pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .cm-debug{position:fixed; right:16px; bottom:16px; width:480px; max-width:96vw;
            background:#111; color:#eee; border-radius:12px; z-index:2147483000; }
            .cm-head{background:#222; border-top-left-radius:12px; border-top-right-radius:12px; }
            .cm-body{background:#151515; border-bottom-left-radius:12px; border-bottom-right-radius:12px; }
            .cm-min{height:36px; overflow:hidden; }
            .cm-full{inset:0 0 0 0; margin:0; width:auto; height:auto; border-radius:0; }
        </style>`;
            document.body.insertAdjacentHTML('beforeend', html);
            const el = document.getElementById('cm-debug');
            el.querySelector('[data-cm="close"]').addEventListener('click', () => this.hide());
            el.querySelector('[data-cm="minimize"]').addEventListener('click', () => this.minimizeToggle());
            el.querySelector('[data-cm="fullscreen"]').addEventListener('click', () => this.fullscreenToggle());
            this.isReady = true; this.refreshAll();
        },
        show() { this.ensure(); this.isVisible = true; document.getElementById('cm-debug').style.display = 'block'; this.refreshAll(); },
        hide() { this.isVisible = false; const el = document.getElementById('cm-debug'); if (el) el.style.display = 'none'; },
        toggle() { (this.isVisible ? this.hide() : this.show()); },
        minimizeToggle() { const el = document.getElementById('cm-debug'); if (!el) return; this.isMinimized = !this.isMinimized; el.classList.toggle('cm-min', this.isMinimized); },
        fullscreenToggle() { const el = document.getElementById('cm-debug'); if (!el) return; this.isFullscreen = !this.isFullscreen; el.classList.toggle('cm-full', this.isFullscreen); },

        refreshTitleAndStateTimer() {
            if (!this.isReady) return;
            const st = Store.state;
            const titleState = document.getElementById('cm-title-state');
            const titleTimer = document.getElementById('cm-title-timer');
            if (titleState) titleState.textContent = st.current;
            // pick current state's recent timer
            const r = Store.data.recent;
            let sec = 0;
            if (st.current === 'RUNNING' || st.current === 'MANUAL_OP') sec = r.operativeSec;
            else if (st.current === 'PAUSED') sec = r.pausedSec;
            else if (st.current === 'IDLE') sec = r.idleSec;
            if (titleTimer) titleTimer.textContent = secToHMS(sec);
        },

        refreshTags() {
            if (!this.isReady) return; const s = Store.tags; const set = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = String(v); };
            set('cm-t-611', s[611]); set('cm-t-613', s[613]); set('cm-t-614', s[614]);
        },

        refreshState() {
            if (!this.isReady) return;
            const st = Store.state; const set = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v || '-'; };
            set('cm-state', st.current); set('cm-since', st.sinceMysql); set('cm-last', st.lastChangeAtMysql);
            this.refreshTitleAndStateTimer();
        },

        refreshTotals() {
            if (!this.isReady) return;
            const t = Store.data.totals, r = Store.data.recent;
            const set = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v; };
            // totals
            set('cm-op', secToHMS(t.operativeSec));
            set('cm-pause', secToHMS(t.pausedSec));
            set('cm-idle', secToHMS(t.idleSec));
            // recent
            set('cm-op-recent', secToHMS(r.operativeSec));
            set('cm-pause-recent', secToHMS(r.pausedSec));
            set('cm-idle-recent', secToHMS(r.idleSec));
        },

        refreshLogs() {
            if (!this.isReady) return; const box = document.getElementById('cm-logs'); if (!box) return;
            box.textContent = Store.logs.map(l => `[${l.time}] ${l.msg}`).join('\n'); box.scrollTop = 0;
        },

        refreshAll() { this.refreshTags(); this.refreshState(); this.refreshTotals(); this.refreshLogs(); this.refreshTitleAndStateTimer(); }
    };

    // --- Reason Modal (blocking) ---
    const ReasonModal = {
        el: null, modal: null,
        ensure() {
            if (this.el) return;
            const html = `
        <div class="modal fade" id="cm-reason-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-2"><h6 class="modal-title">Select Reason</h6></div>
                    <div class="modal-body">
                        <div class="mb-2"><small id="cm-reason-help" class="text-muted"></small></div>
                        <div id="cm-reason-list" class="list-group"></div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" id="cm-reason-cancel" disabled>Cancel</button>
                    </div>
                </div>
            </div>
        </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            this.el = document.getElementById('cm-reason-modal');
            this.modal = new bootstrap.Modal(this.el);
        },
        open(type) {
            this.ensure();
            const listEl = document.getElementById('cm-reason-list'); const help = document.getElementById('cm-reason-help');
            listEl.innerHTML = '';
            const reasons = (type === 'PAUSE') ? Store.cfg.pauseReasons : Store.cfg.idleReasons;
            help.textContent = `Reason required for ${type === 'PAUSE' ? 'Pause' : 'Idle'} (exceeded threshold)`;
            reasons.forEach(r => {
                const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'list-group-item list-group-item-action';
                btn.textContent = r.label; btn.addEventListener('click', () => {
                    if (confirm(`Are you sure you want to select "${r.label}" as the reason?`)) {
                        ReasonModal.modal.hide();
                        ReasonModal.resolve(type, r);
                    }
                });
                listEl.appendChild(btn);
            });
            this.modal.show();
        },
        resolve(type, reason) {
            const pending = Store.state.pendingReason; if (!pending || pending.type !== type) return;
            if (type === 'PAUSE') {
                const rec = Store.data.pauses[pending.idx];
                if (rec) {
                    rec.reasonId = reason.id;
                    rec.reasonLabel = reason.label;
                    Store.pushLog(`Pause reason set: ${reason.label}`);


                }
            }
            else {
                const rec = Store.data.idles[pending.idx];
                if (rec) {
                    rec.reasonId = reason.id;
                    rec.reasonLabel = reason.label;
                    Store.pushLog(`Idle reason set: ${reason.label}`);


                }
            }
            Store.state.pendingReason = null; CycleMonitor._mirrorIntoProgramLogic();
        }
    };

    // --- FSM / Evaluator ---
    function evaluateState() {
        const g = Store.tags;
        let next;
        if (g[611]) next = 'RUNNING';
        else if (g[613]) next = 'PAUSED';
        else if (!g[611] && !g[613] && g[614]) next = 'IDLE';
        else next = 'MANUAL_OP';

        const prev = Store.state.current;
        if (prev !== next) {
            const now = nowMysqlIST();
            Store.state.current = next; Store.state.lastChangeAtMysql = now; Store.state.sinceMysql = now;

            // RESET all recent timers on every state change; current state will start counting from 0
            Store.data.recent.operativeSec = 0;
            Store.data.recent.pausedSec = 0;
            Store.data.recent.idleSec = 0;

            DebugPanel.refreshState();
            Store.pushLog(`State change: ${prev} -> ${next}`);
            onStateBoundary(prev, next, now);
        }
    }

    function onStateBoundary(prev, next, nowMysql) {
        // Cycle start (RUNNING rising, not resuming from PAUSED)
        if (next === 'RUNNING' && prev !== 'RUNNING') {
            if (prev !== 'PAUSED') {
                Store.state.lastCycle = { startAt: nowMysql, endAt: null, durationSec: 0 };
                Store.data.cycles.push(Store.state.lastCycle);
                Store.pushLog(`Cycle started at ${nowMysql}`);
            }
        }
        // Cycle end when leaving RUNNING and 611=0 & 613=0
        const g = Store.tags;
        if (prev === 'RUNNING' && !g[611] && !g[613]) {
            const last = Store.state.lastCycle;
            if (last && !last.endAt) {
                last.endAt = nowMysql;
                last.durationSec = Math.max(0, Math.floor((Date.parse(nowMysql.replace(' ', 'T')) - Date.parse(last.startAt.replace(' ', 'T'))) / 1000));
                Store.pushLog(`Cycle ended at ${nowMysql}, duration ${secToHMS(last.durationSec)}`);

                // Log completed RUNNING cycle
                if (window.logCompletedStateRecord) {
                    window.logCompletedStateRecord('RUNNING', last);
                }

                Store.state.lastCycle = null;
            }
        }
        // Pause boundaries
        if (next === 'PAUSED' && prev !== 'PAUSED') {
            const rec = { startAt: nowMysql, endAt: null, durationSec: 0, reasonId: null, reasonLabel: null, cycleIndex: Store.data.cycles.length - 1 >= 0 ? (Store.data.cycles.length - 1) : null };
            Store.data.pauses.push(rec); Store.pushLog(`Pause started at ${nowMysql}`);
        }
        if (prev === 'PAUSED' && next !== 'PAUSED') {
            const openIdx = Store.data.pauses.findIndex(p => p.endAt === null);
            if (openIdx >= 0) {
                const rec = Store.data.pauses[openIdx]; rec.endAt = nowMysql;
                rec.durationSec = Math.max(0, Math.floor((Date.parse(nowMysql.replace(' ', 'T')) - Date.parse(rec.startAt.replace(' ', 'T'))) / 1000));
                Store.pushLog(`Pause ended at ${nowMysql}, duration ${secToHMS(rec.durationSec)}`);

                // Log completed PAUSED record (with reason if available)
                if (window.logCompletedStateRecord) {
                    window.logCompletedStateRecord('PAUSED', rec);
                }
            }
        }
        // Idle boundaries
        if (next === 'IDLE' && prev !== 'IDLE') {
            const rec = { startAt: nowMysql, endAt: null, durationSec: 0, reasonId: null, reasonLabel: null };
            Store.data.idles.push(rec); Store.pushLog(`Idle started at ${nowMysql}`);
        }
        if (prev === 'IDLE' && next !== 'IDLE') {
            const openIdx = Store.data.idles.findIndex(p => p.endAt === null);
            if (openIdx >= 0) {
                const rec = Store.data.idles[openIdx]; rec.endAt = nowMysql;
                rec.durationSec = Math.max(0, Math.floor((Date.parse(nowMysql.replace(' ', 'T')) - Date.parse(rec.startAt.replace(' ', 'T'))) / 1000));
                Store.pushLog(`Idle ended at ${nowMysql}, duration ${secToHMS(rec.durationSec)}`);

                // Log completed IDLE record (with reason if available)
                if (window.logCompletedStateRecord) {
                    window.logCompletedStateRecord('IDLE', rec);
                }
            }
        }
        CycleMonitor._mirrorIntoProgramLogic();
    }

    function scheduleEvaluate() {
        if (Store.state.lastDebounceTimer) clearTimeout(Store.state.lastDebounceTimer);
        Store.state.lastDebounceTimer = setTimeout(() => { Store.state.lastDebounceTimer = null; evaluateState(); DebugPanel.refreshTags(); }, Store.cfg.debounceMs);
    }

    function tick() {
        const now = Date.now(); const dtSec = (now - Store.data.lastTickAt) / 1000; Store.data.lastTickAt = now;

        switch (Store.state.current) {
            case 'RUNNING':
            case 'MANUAL_OP':
                Store.data.totals.operativeSec += dtSec;
                Store.data.recent.operativeSec += dtSec;
                break;
            case 'PAUSED':
                Store.data.totals.pausedSec += dtSec;
                Store.data.recent.pausedSec += dtSec;
                handleThresholdReason('PAUSE');
                break;
            case 'IDLE':
                Store.data.totals.idleSec += dtSec;
                Store.data.recent.idleSec += dtSec;
                handleThresholdReason('IDLE');
                break;
        }

        DebugPanel.refreshTotals();
        DebugPanel.refreshTitleAndStateTimer();
        CycleMonitor._mirrorIntoProgramLogic();
        requestAnimationFrame(() => setTimeout(tick, 250));
    }

    function handleThresholdReason(kind) {
        if (Store.state.pendingReason) return;
        if (kind === 'PAUSE') {
            const idx = Store.data.pauses.findIndex(p => p.endAt === null); if (idx < 0) return;
            const rec = Store.data.pauses[idx]; const durSec = (Date.now() - Date.parse(rec.startAt.replace(' ', 'T'))) / 1000;
            if (durSec >= Store.cfg.pauseThresholdSec && !rec.reasonId) {
                Store.state.pendingReason = { type: 'PAUSE', idx, openedAtMysql: nowMysqlIST() };
                Store.pushLog('Pause exceeded threshold; asking reason.'); ReasonModal.open('PAUSE');
            }
        } else {
            const idx = Store.data.idles.findIndex(p => p.endAt === null); if (idx < 0) return;
            const rec = Store.data.idles[idx]; const durSec = (Date.now() - Date.parse(rec.startAt.replace(' ', 'T'))) / 1000;
            if (durSec >= Store.cfg.idleThresholdSec && !rec.reasonId) {
                Store.state.pendingReason = { type: 'IDLE', idx, openedAtMysql: nowMysqlIST() };
                Store.pushLog('Idle exceeded threshold; asking reason.'); ReasonModal.open('IDLE');
            }
        }
    }

    // --- PLC subscription helpers ---
    function _unbindTagHandlers() {
        const T = Store.cfg.tags, H = Store.handlers, dl = Store.PlcDataLayer;
        if (!dl || !dl.offTagChange) { H[611] = H[613] = H[614] = null; return; }
        try {
            if (H[611]) dl.offTagChange(T.AUTO_RUNNING, H[611]);
            if (H[613]) dl.offTagChange(T.AUTO_PAUSE, H[613]);
            if (H[614]) dl.offTagChange(T.MACHINE_IDLE, H[614]);
        } catch (e) { }
        H[611] = H[613] = H[614] = null;
    }
    function _bindTagHandlers() {
        const T = Store.cfg.tags, H = Store.handlers, dl = Store.PlcDataLayer;
        if (!dl || !dl.onTagChange) throw new Error('PlcDataLayer missing onTagChange(tagId, handler)');
        H[611] = (v) => { Store.tags[611] = Number(!!v); scheduleEvaluate(); };
        H[613] = (v) => { Store.tags[613] = Number(!!v); scheduleEvaluate(); };
        H[614] = (v) => { Store.tags[614] = Number(!!v); scheduleEvaluate(); };
        dl.onTagChange(T.AUTO_RUNNING, H[611]);
        dl.onTagChange(T.AUTO_PAUSE, H[613]);
        dl.onTagChange(T.MACHINE_IDLE, H[614]);
        DebugPanel.refreshTags();
    }

    // --- Public API (globals) ---
    const CycleMonitor = {
        configure(partialCfg) { Store.cfg = { ...Store.cfg, ...partialCfg }; },
        init() {
            const boot = () => {
                // use already-exposed globals (assign them to window.* if needed before calling init)
                if (!w.programLogic || !w.PlcDataLayer) { setTimeout(boot, 100); return; }
                Store.programLogic = w.programLogic;
                Store.PlcDataLayer = w.PlcDataLayer;

                // Ensure properties exist ON THE SAME OBJECT (no reassigning)
                const pl = Store.programLogic;
                if (!pl.machineState) {
                    pl.machineState = {
                        current: 'INIT', since: null, lastChangeAt: null,
                        totals: { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' },
                        recent: { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' }
                    };
                } else if (!pl.machineState.recent) {
                    pl.machineState.recent = { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' };
                }
                // Keep direct references so console shows live arrays
                pl.cycles = Store.data.cycles;
                pl.pauses = Store.data.pauses;
                pl.idles = Store.data.idles;

                this._mirrorIntoProgramLogic();
                _bindTagHandlers();

                DebugPanel.ensure();
                Store.pushLog('CycleMonitor initialized (v1.3).');
                Store.data.lastTickAt = Date.now();
                tick();
            };
            boot();
        },
        // If you recreate programLogic later:
        bindProgramLogic() {
            if (w.programLogic) Store.programLogic = w.programLogic;
            const pl = Store.programLogic;
            if (!pl.machineState) {
                pl.machineState = {
                    current: 'INIT', since: null, lastChangeAt: null,
                    totals: { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' },
                    recent: { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' }
                };
            } else if (!pl.machineState.recent) {
                pl.machineState.recent = { operative: '00:00:00', paused: '00:00:00', idle: '00:00:00' };
            }
            pl.cycles = Store.data.cycles;
            pl.pauses = Store.data.pauses;
            pl.idles = Store.data.idles;
            this._mirrorIntoProgramLogic();
            Store.pushLog('programLogic (re)bound.');
        },
        resubscribe(newPlc) {
            _unbindTagHandlers();
            if (newPlc) Store.PlcDataLayer = newPlc; else Store.PlcDataLayer = w.PlcDataLayer;
            _bindTagHandlers();
            Store.pushLog('Re-subscribed to PLC tag changes.');
        },
        DebugPanel: {
            toggle: () => DebugPanel.toggle(),
            show: () => DebugPanel.show(),
            hide: () => DebugPanel.hide()
        },
        _mirrorIntoProgramLogic() {
            const pl = Store.programLogic, st = Store.state, tot = Store.data.totals, r = Store.data.recent;
            if (!pl || !pl.machineState) return;
            pl.machineState.current = st.current;
            pl.machineState.since = st.sinceMysql;
            pl.machineState.lastChangeAt = st.lastChangeAtMysql;
            // totals
            pl.machineState.totals.operative = secToHMS(tot.operativeSec);
            pl.machineState.totals.paused = secToHMS(tot.pausedSec);
            pl.machineState.totals.idle = secToHMS(tot.idleSec);
            // recent
            pl.machineState.recent.operative = secToHMS(r.operativeSec);
            pl.machineState.recent.paused = secToHMS(r.pausedSec);
            pl.machineState.recent.idle = secToHMS(r.idleSec);
        }
    };

    w.CycleMonitor = CycleMonitor;

    // State logging function for timerCounters - logs completed time records
    w.logCompletedStateRecord = function (recordType, recordData) {
        // Get programId from global programLogic if available
        let programId = null;
        if (window.programLogic && window.programLogic.programId && window.programLogic.programId > 0) {
            programId = window.programLogic.programId;
        }

        const postData = {
            recordType: recordType, // 'RUNNING', 'PAUSED', 'IDLE'
            startTime: recordData.startAt,
            endTime: recordData.endAt,
            durationSec: recordData.durationSec,
            reasonId: recordData.reasonId || null,
            reasonLabel: recordData.reasonLabel || null,
            cycleIndex: recordData.cycleIndex || null,
            programId: programId
        };

        // Call backend API to log completed state record (silent - no user notifications)
        if (window.apiCall) {
            window.apiCall("POST", "api/productionMaster/logCompletedStateRecord", postData)
                .then(function (response) {
                    if (response.status) {
                        console.log(`${recordType} record logged successfully:`, response.data);
                    } else {
                        console.error(`Failed to log ${recordType} record:`, response.message);
                    }
                })
                .catch(function (error) {
                    console.error(`Error logging ${recordType} record:`, error);
                });
        } else {
            console.warn("apiCall function not available for state logging");
        }
    };
})(window);