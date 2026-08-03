<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --dd-bg:#090d12;
        --dd-surface:#11161d;
        --dd-surface-alt:#171f29;
        --dd-border:rgba(255,255,255,.07);
        --dd-border-strong:rgba(255,255,255,.14);
        --dd-text:#f3f6f7;
        --dd-text-sec:#93a2ac;
        --dd-text-muted:#586872;
        --dd-accent:#34f5c5;
        --dd-accent-dim:#0f6e56;
        --dd-gold:#ffb020;
        --dd-silver:#c9d2da;
        --dd-bronze:#d8854a;
        --dd-rank-4:#38bdf8;
        --dd-rank-5:#a78bfa;
        --dd-rank-6:#fb7185;
        --dd-danger:#ff5a5a;
        --dd-font-display:'Space Grotesk',sans-serif;
        --dd-font-body:'Inter',sans-serif;
        --dd-font-mono:'JetBrains Mono',monospace;
    }
    .dd-wrap{background:var(--dd-bg);color:var(--dd-text);font-family:var(--dd-font-body);
        padding:28px;border-radius:18px;min-height:100vh}
    .dd-wrap *{box-sizing:border-box}
    .dd-topbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:28px}
    .dd-brand{display:flex;align-items:center;gap:12px}
    .dd-brand-mark{width:38px;height:38px;border-radius:10px;background:linear-gradient(160deg,var(--dd-accent),var(--dd-accent-dim));
        display:flex;align-items:center;justify-content:center}
    .dd-brand-mark svg{width:20px;height:20px}
    .dd-brand-title{font-family:var(--dd-font-display);font-weight:700;font-size:19px;letter-spacing:.2px}
    .dd-brand-sub{font-size:12px;color:var(--dd-text-muted);margin-top:1px}
    .dd-sync{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--dd-text-sec);
        background:var(--dd-surface);border:1px solid var(--dd-border);padding:7px 12px;border-radius:999px}
    .dd-nyclock{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--dd-accent);
        background:var(--dd-surface);border:1px solid rgba(52,245,197,.25);padding:7px 12px;border-radius:999px;
        font-family:var(--dd-font-mono)}
    .dd-update-btn{display:inline-flex;align-items:center;gap:7px;background:var(--dd-accent);color:#06231b;
        border:none;border-radius:999px;padding:8px 15px;font-size:12.5px;font-weight:600;cursor:pointer;
        font-family:var(--dd-font-body);transition:filter .15s ease}
    .dd-update-btn:hover{filter:brightness(1.08)}
    .dd-update-btn i{font-size:14px}
    .dd-readonly-badge{display:inline-flex;align-items:center;gap:7px;font-size:12px;color:var(--dd-text-muted);
        background:var(--dd-surface);border:1px solid var(--dd-border);padding:7px 12px;border-radius:999px}
    .dd-readonly-badge i{font-size:14px}
    .dd-dot{width:7px;height:7px;border-radius:50%;background:var(--dd-accent);
        box-shadow:0 0 0 0 rgba(52,245,197,.6);animation:dd-pulse 2s infinite}
    @keyframes dd-pulse{
        0%{box-shadow:0 0 0 0 rgba(52,245,197,.5)}
        70%{box-shadow:0 0 0 7px rgba(52,245,197,0)}
        100%{box-shadow:0 0 0 0 rgba(52,245,197,0)}
    }
    .dd-filters{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:26px}
    .dd-field{display:flex;flex-direction:column;gap:5px}
    .dd-label{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--dd-text-muted)}
    .dd-select,.dd-input{background:var(--dd-surface);border:1px solid var(--dd-border);color:var(--dd-text);
        border-radius:9px;padding:9px 12px;font-size:13px;font-family:var(--dd-font-body);outline:none;min-width:150px}
    .dd-select:focus,.dd-input:focus{border-color:var(--dd-accent-dim)}

    .flatpickr-calendar{background:var(--dd-surface);border:1px solid var(--dd-border-strong);
        box-shadow:0 12px 28px rgba(0,0,0,.45);font-family:var(--dd-font-body)}
    .flatpickr-calendar.arrowTop:before,.flatpickr-calendar.arrowTop:after{border-bottom-color:var(--dd-surface)}
    .flatpickr-months,.flatpickr-weekdays{background:var(--dd-surface-alt)}
    .flatpickr-month,.flatpickr-weekday{color:var(--dd-text-sec)!important;fill:var(--dd-text-sec)}
    .flatpickr-current-month .flatpickr-monthDropdown-months,.flatpickr-current-month input.cur-year{color:var(--dd-text)}
    .flatpickr-day{color:var(--dd-text)}
    .flatpickr-day.flatpickr-disabled,.flatpickr-day.prevMonthDay,.flatpickr-day.nextMonthDay{color:var(--dd-text-muted)}
    .flatpickr-day:hover{background:var(--dd-surface-alt)}
    .flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange{
        background:var(--dd-accent)!important;border-color:var(--dd-accent)!important;color:#06231b!important}
    .flatpickr-day.inRange{background:rgba(52,245,197,.14)!important;border-color:transparent!important;box-shadow:none}
    .flatpickr-prev-month svg,.flatpickr-next-month svg{fill:var(--dd-text-sec)}
    .dd-presets{display:flex;gap:6px;align-self:flex-end}
    .dd-chip{font-size:12px;color:var(--dd-text-sec);background:var(--dd-surface);border:1px solid var(--dd-border);
        padding:8px 11px;border-radius:8px;cursor:pointer;font-family:var(--dd-font-body)}
    .dd-chip.active,.dd-chip:hover{border-color:var(--dd-accent-dim);color:var(--dd-accent)}
    .dd-apply{align-self:flex-end;background:var(--dd-accent);color:#06231b;border:none;border-radius:9px;
        padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--dd-font-body)}
    .dd-apply:hover{filter:brightness(1.08)}

   .dd-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px}
    .dd-stat-card{background:var(--dd-surface);border:1px solid var(--dd-border);border-radius:14px;padding:18px;position:relative;overflow:hidden}
    .dd-stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;
        background:linear-gradient(90deg,var(--dd-accent-dim),var(--dd-accent));opacity:.7}
    .dd-stat-icon{width:30px;height:30px;border-radius:9px;background:rgba(52,245,197,.1);
        color:var(--dd-accent);display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:12px}
    .dd-stat-label{font-size:12px;color:var(--dd-text-sec);margin-bottom:10px}
    .dd-stat-value{font-family:var(--dd-font-mono);font-size:26px;font-weight:600;letter-spacing:-.02em}
    .dd-stat-trend{font-size:12px;margin-top:6px;display:flex;align-items:center;gap:4px}
    .dd-trend-up{color:var(--dd-accent)}
    .dd-trend-down{color:var(--dd-danger)}
    .dd-wave{position:absolute;right:14px;bottom:14px;display:flex;align-items:flex-end;gap:2px;height:26px;opacity:.85}
    .dd-wave span{width:3px;background:var(--dd-accent-dim);border-radius:2px;animation:dd-bar 1.2s ease-in-out infinite}
    .dd-wave span:nth-child(1){height:40%;animation-delay:.0s}
    .dd-wave span:nth-child(2){height:80%;animation-delay:.1s}
    .dd-wave span:nth-child(3){height:55%;animation-delay:.2s}
    .dd-wave span:nth-child(4){height:95%;animation-delay:.3s}
    .dd-wave span:nth-child(5){height:35%;animation-delay:.4s}
    .dd-wave span:nth-child(6){height:70%;animation-delay:.5s}
    @keyframes dd-bar{0%,100%{transform:scaleY(.6)}50%{transform:scaleY(1)}}

    .dd-grid{display:flex;flex-direction:column;gap:16px}
    @media(max-width:820px){.dd-stats{grid-template-columns:repeat(2,1fr)}}

    .dd-panel{background:var(--dd-surface);border:1px solid var(--dd-border);border-radius:14px;padding:20px}
    .dd-panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
    .dd-panel-title{font-family:var(--dd-font-display);font-size:15px;font-weight:700}
    .dd-panel-sub{font-size:12px;color:var(--dd-text-muted);margin-top:2px}
    .dd-legend{display:flex;gap:14px;font-size:12px;color:var(--dd-text-sec)}
    .dd-legend span.sw{display:inline-block;width:8px;height:8px;border-radius:2px;margin-right:5px}

    .dd-lb-scroll{overflow-x:auto}
    .dd-lb-table{width:100%;border-collapse:collapse;min-width:820px;}
    .dd-lb-table th{font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;color:var(--dd-text-muted);
    text-align:right;padding:0 6px 8px;font-weight:600;white-space:nowrap}
    .dd-lb-table th:first-child{text-align:left}
    .dd-lb-table th.dd-text-left,.dd-lb-table td.dd-text-left{text-align:left!important}
    .dd-lb-table th.dd-th-group-monthly{text-align:center;color:var(--dd-text-sec);border-bottom:1px solid var(--dd-border)}
    .dd-lb-table td{padding:7px 6px;font-size:12.5px;border-bottom:1px solid var(--dd-border);text-align:right;
    font-family:var(--dd-font-mono);white-space:nowrap}
    
    /* Top 3 row Green colors for Monthly Performance Table only */
    #ddMonthlyTable tbody tr.r1 td, #ddMonthlyTable tbody tr.r2 td, #ddMonthlyTable tbody tr.r3 td,
    #ddMonthlyTable tbody tr.r1 .dd-lb-team, #ddMonthlyTable tbody tr.r2 .dd-lb-team, #ddMonthlyTable tbody tr.r3 .dd-lb-team {
        color: #22c55e !important;
    }
    #ddMonthlyTable tbody tr.r4 td,
    #ddMonthlyTable tbody tr.r4 .dd-lb-team {
        color: var(--dd-rank-4) !important;
    }
    #ddMonthlyTable tbody tr.r5 td,
    #ddMonthlyTable tbody tr.r5 .dd-lb-team {
        color: var(--dd-rank-5) !important;
    }
    #ddMonthlyTable tbody tr.r6 td,
    #ddMonthlyTable tbody tr.r6 .dd-lb-team {
        color: var(--dd-rank-6) !important;
    }

    .dd-lb-table tbody td:first-child{text-align:left;font-family:var(--dd-font-body)}
    .dd-lb-table tbody td.dd-font-body{font-family:var(--dd-font-body)}
    .dd-lb-table tbody tr{transition:background .2s ease}
    .dd-lb-table tbody tr:hover{background:rgba(255,255,255,.025)}
    .dd-lb-table tbody tr:last-child td{border-bottom:none}
    .dd-lb-table tfoot td{padding:11px 10px;font-size:12.5px;font-weight:600;border-top:1px solid var(--dd-border-strong);
        background:var(--dd-surface-alt);font-family:var(--dd-font-mono);text-align:right}
    .dd-lb-table tfoot td:first-child{font-family:var(--dd-font-body);text-align:left}
    .dd-lb-agent{display:flex;align-items:center;gap:9px}
    .dd-avatar{width:28px;height:28px;border-radius:50%;background:var(--dd-surface-alt);color:var(--dd-text-sec);
        display:flex;align-items:center;justify-content:center;font-size:10.5px;font-weight:600;
        font-family:var(--dd-font-mono);flex-shrink:0;border:1px solid var(--dd-border)}
    .dd-lb-table tr.r1 .dd-avatar{border-color:var(--dd-gold);color:var(--dd-gold)}
    .dd-lb-table tr.r2 .dd-avatar{border-color:var(--dd-silver);color:var(--dd-silver)}
    .dd-lb-table tr.r3 .dd-avatar{border-color:var(--dd-bronze);color:var(--dd-bronze)}
    #ddMonthlyTable tr.r4 .dd-avatar{border-color:var(--dd-rank-4);color:var(--dd-rank-4);background:rgba(56,189,248,.08)}
    #ddMonthlyTable tr.r5 .dd-avatar{border-color:var(--dd-rank-5);color:var(--dd-rank-5);background:rgba(167,139,250,.08)}
    #ddMonthlyTable tr.r6 .dd-avatar{border-color:var(--dd-rank-6);color:var(--dd-rank-6);background:rgba(251,113,133,.08)}
    .dd-rank-badge{width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;
        font-family:var(--dd-font-mono);font-size:11px;font-weight:600;background:var(--dd-surface-alt);color:var(--dd-text-sec)}
    tr.r1 .dd-rank-badge{background:rgba(255,176,32,.16);color:var(--dd-gold);animation:dd-trophy-pulse 2.2s ease-in-out infinite}
    tr.r2 .dd-rank-badge{background:rgba(201,210,218,.14);color:var(--dd-silver)}
    tr.r3 .dd-rank-badge{background:rgba(216,133,74,.16);color:var(--dd-bronze)}
    #ddMonthlyTable tr.r4 .dd-rank-badge{background:rgba(56,189,248,.14);color:var(--dd-rank-4);border:1px solid rgba(56,189,248,.28)}
    #ddMonthlyTable tr.r5 .dd-rank-badge{background:rgba(167,139,250,.14);color:var(--dd-rank-5);border:1px solid rgba(167,139,250,.28)}
    #ddMonthlyTable tr.r6 .dd-rank-badge{background:rgba(251,113,133,.14);color:var(--dd-rank-6);border:1px solid rgba(251,113,133,.28)}
    .dd-lb-name{font-size:13px;font-weight:500}
    .dd-lb-team{font-size:11px;color:var(--dd-text-muted)}
    .dd-total-closers{display:inline-flex;align-items:center;justify-content:center;border-radius:6px;padding:2px 8px;
        background:rgba(52,245,197,.12);border:1px solid rgba(52,245,197,.24);color:var(--dd-accent);font-family:var(--dd-font-mono);font-weight:600}
    .dd-pill{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11.5px;font-weight:600}
    .dd-pill-hi{background:rgba(52,245,197,.14);color:var(--dd-accent)}
    .dd-pill-lo{background:rgba(255,90,90,.14);color:var(--dd-danger)}
    .dd-time-danger{color:var(--dd-danger);font-weight:600}
    .dd-time-ok{color:var(--dd-text-sec)}

    .dd-footnote{margin-top:18px;font-size:11.5px;color:var(--dd-text-muted);display:flex;align-items:center;gap:8px}
    .dd-footnote .dd-dot-static{width:6px;height:6px;border-radius:50%;background:var(--dd-text-muted)}

    #ddSaleCelebration{position:fixed;inset:0;z-index:99999;pointer-events:none;display:none;overflow:hidden;
        background:radial-gradient(circle at 50% 65%, rgba(52,245,197,.05), rgba(9,13,18,0) 60%);
        opacity:0;transition:opacity .5s ease}
    #ddSaleCelebration.show{display:block;opacity:1}

    .dd-sale-bubbles{position:absolute;inset:0}
    .dd-sale-bubble{position:absolute;bottom:-70px;border-radius:50%;
        background:radial-gradient(circle at 32% 28%, rgba(255,255,255,.5), rgba(52,245,197,.22) 55%, rgba(52,245,197,.02) 78%);
        border:1px solid rgba(255,255,255,.14);opacity:0;
        animation:dd-bubble-rise 4.2s cubic-bezier(.22,.61,.36,1) forwards}
    @keyframes dd-bubble-rise{
        0%{opacity:0;transform:translate(0,0) scale(.5)}
        8%{opacity:.85}
        50%{transform:translate(var(--dd-drift,14px),-52vh) scale(1)}
        85%{opacity:.4}
        100%{opacity:0;transform:translate(calc(var(--dd-drift,14px) * 2),-104vh) scale(1.1)}
    }

    .dd-sale-banner{position:absolute;top:47%;left:50%;transform:translate(-50%,-46%) scale(.85);
        opacity:0;text-align:center;animation:dd-banner-pop .6s cubic-bezier(.22,1,.36,1) forwards,
        dd-banner-out .6s ease forwards 14.25s}
    @keyframes dd-banner-pop{0%{opacity:0;transform:translate(-50%,-40%) scale(.85)}100%{opacity:1;transform:translate(-50%,-50%) scale(1)}}
    @keyframes dd-banner-out{to{opacity:0;transform:translate(-50%,-55%) scale(.96)}}

    .dd-sale-pill{display:inline-flex;flex-direction:column;align-items:center;gap:6px;position:relative;overflow:hidden;
        background:linear-gradient(180deg,rgba(17,22,29,.92),rgba(9,13,18,.96));
        backdrop-filter:blur(16px);border:2px solid rgba(52,245,197,.4);
        border-radius:26px;padding:32px 56px;box-shadow:0 24px 60px rgba(0,0,0,.75),0 0 45px rgba(52,245,197,.22);min-width:440px}
    .dd-sale-pill::before{content:'';position:absolute;inset:0;
        background:linear-gradient(120deg,rgba(52,245,197,0),rgba(52,245,197,.16),rgba(255,176,32,.12),rgba(52,245,197,0));
        transform:translateX(-100%);animation:dd-sale-sweep 2.2s ease-in-out infinite}
    @keyframes dd-sale-sweep{50%,100%{transform:translateX(100%)}}
    .dd-sale-kicker{font-size:13px;text-transform:uppercase;letter-spacing:.22em;color:var(--dd-accent);font-weight:700;margin-bottom:4px}
    .dd-sale-name{font-family:var(--dd-font-display);font-weight:700;font-size:52px;letter-spacing:.2px;
        background:linear-gradient(100deg,var(--dd-text) 30%,var(--dd-accent) 55%,var(--dd-text) 78%);
        background-size:220% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;
        animation:dd-shimmer 2.6s linear infinite;position:relative;z-index:1;text-shadow:0 0 28px rgba(52,245,197,.18)}
    @keyframes dd-shimmer{0%{background-position:0% 0}100%{background-position:-220% 0}}
    .dd-sale-sub{font-size:16px;color:var(--dd-text-sec);margin-top:4px;letter-spacing:.02em;font-weight:500}

    .dd-dancers{position:absolute;inset:0;pointer-events:none}
    .dd-dance-spot{position:absolute;bottom:9%;left:50%;transform:translateX(-50%);
        width:120px;height:26px;border-radius:50%;
        background:radial-gradient(ellipse at center, rgba(52,245,197,.28), rgba(52,245,197,0) 72%);
        opacity:0;animation:dd-spot-in .5s ease forwards .15s, dd-spot-pulse 1.8s ease-in-out infinite .5s}
    @keyframes dd-spot-in{to{opacity:1}}
    @keyframes dd-spot-pulse{0%,100%{transform:translateX(-50%) scale(1);opacity:.7}50%{transform:translateX(-50%) scale(1.15);opacity:.95}}
    .dd-dancer{position:absolute;bottom:10.5%;left:50%;font-size:58px;opacity:0;
        animation:dd-dancer-in .5s cubic-bezier(.22,1,.36,1) forwards .1s,
        dd-dance 1.8s ease-in-out infinite .6s;
        filter:drop-shadow(0 10px 14px rgba(0,0,0,.45))}
    @keyframes dd-dancer-in{to{opacity:1;transform:translateX(-50%) translateY(0)}}
    @keyframes dd-dance{
        0%,100%{transform:translateX(-50%) translateY(0) rotate(-6deg)}
        25%{transform:translateX(-50%) translateY(-10px) rotate(5deg)}
        50%{transform:translateX(-50%) translateY(-2px) rotate(-3deg)}
        75%{transform:translateX(-50%) translateY(-12px) rotate(7deg)}
    }

    .dd-lightbeams{position:absolute;inset:0;overflow:hidden;mix-blend-mode:screen;opacity:0;
        animation:dd-lights-in .6s ease forwards}
    @keyframes dd-lights-in{to{opacity:1}}
    .dd-beam{position:absolute;top:-10%;width:160px;height:140%;
        transform-origin:top center;opacity:.55;
        animation:dd-beam-sway 3.2s ease-in-out infinite}
    .dd-beam-1{left:8%;  background:linear-gradient(180deg,rgba(52,245,197,.30),rgba(52,245,197,0) 72%);
        transform:rotate(-16deg);animation-delay:0s}
    .dd-beam-2{left:34%; background:linear-gradient(180deg,rgba(255,255,255,.20),rgba(255,255,255,0) 72%);
        transform:rotate(-4deg);animation-delay:.5s}
    .dd-beam-3{left:58%; background:linear-gradient(180deg,rgba(255,176,32,.24),rgba(255,176,32,0) 72%);
        transform:rotate(8deg);animation-delay:1s}
    .dd-beam-4{left:80%; background:linear-gradient(180deg,rgba(52,245,197,.22),rgba(52,245,197,0) 72%);
        transform:rotate(18deg);animation-delay:1.5s}
    @keyframes dd-beam-sway{
        0%,100%{transform:rotate(var(--dd-beam-a,-16deg))}
        50%{transform:rotate(calc(var(--dd-beam-a,-16deg) + 9deg))}
    }
    .dd-beam-1{--dd-beam-a:-16deg}
    .dd-beam-2{--dd-beam-a:-4deg}
    .dd-beam-3{--dd-beam-a:8deg}
    .dd-beam-4{--dd-beam-a:18deg}

    .dd-fireworks{position:absolute;inset:0}
    .dd-fw-flash{position:absolute;width:8px;height:8px;border-radius:50%;
        background:radial-gradient(circle, rgba(255,255,255,.95), rgba(255,255,255,0) 70%);
        transform:translate(-50%,-50%) scale(0);animation:dd-fw-flash .5s ease-out forwards}
    @keyframes dd-fw-flash{0%{opacity:1;transform:translate(-50%,-50%) scale(0)}
        60%{opacity:.7;transform:translate(-50%,-50%) scale(9)}
        100%{opacity:0;transform:translate(-50%,-50%) scale(13)}}
    .dd-fw-particle{position:absolute;width:5px;height:5px;border-radius:50%;
        transform:translate(-50%,-50%);opacity:0;
        animation:dd-fw-particle 1.1s cubic-bezier(.15,.6,.35,1) forwards}
    @keyframes dd-fw-particle{
        0%{opacity:0;transform:translate(-50%,-50%) translate(0,0) scale(1)}
        8%{opacity:1}
        100%{opacity:0;transform:translate(-50%,-50%) translate(var(--dd-fw-x),var(--dd-fw-y)) scale(.4)}
    }

    .dd-stat-card,.dd-panel,.dd-track-card{opacity:0;transform:translateY(10px);animation:dd-rise .5s ease forwards}
    .dd-track-card{animation-delay:.04s}
    .dd-stat-card:nth-child(1){animation-delay:.18s}
    .dd-stat-card:nth-child(2){animation-delay:.25s}
    .dd-stat-card:nth-child(3){animation-delay:.32s}
    .dd-stat-card:nth-child(4){animation-delay:.39s}
    .dd-grid .dd-panel:nth-child(1){animation-delay:.46s}
    .dd-grid .dd-panel:nth-child(2){animation-delay:.52s}
    @keyframes dd-rise{to{opacity:1;transform:translateY(0)}}
    .dd-stat-card{transition:transform .25s ease,border-color .25s ease}
    .dd-stat-card:hover{transform:translateY(-3px);border-color:var(--dd-border-strong)}

    .dd-track-card{background:linear-gradient(180deg,var(--dd-surface) 0%,#0d1319 100%);
        border:1px solid var(--dd-border);border-radius:18px;padding:26px 28px 30px;margin-bottom:24px;
        position:relative;overflow:hidden}
    .dd-track-head{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:34px}
    .dd-track-eyebrow{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--dd-text-muted);margin-bottom:6px}
    .dd-track-title{font-family:var(--dd-font-display);font-weight:700;font-size:20px}
    .dd-track-title b{color:var(--dd-accent)}
    .dd-track-num{text-align:right}
    .dd-track-num .big{font-family:var(--dd-font-mono);font-size:30px;font-weight:600;color:var(--dd-text)}
    .dd-track-num .small{font-size:12px;color:var(--dd-text-muted)}

    .dd-track{position:relative;height:64px;margin:0 6px 18px}
    .dd-track-line{position:absolute;top:28px;left:0;right:0;height:10px;border-radius:999px;
        background:#1b232c;border:1px solid var(--dd-border)}
    .dd-track-line::after{content:'';position:absolute;top:50%;left:8px;right:8px;height:0;
        border-top:2px dashed rgba(255,255,255,.18);transform:translateY(-50%)}
    .dd-track-fill{position:absolute;top:28px;left:0;height:10px;border-radius:999px;
        background:linear-gradient(90deg,var(--dd-accent-dim),var(--dd-accent));
        width:0%;transition:width 1.6s cubic-bezier(.22,1,.36,1);box-shadow:0 0 14px rgba(52,245,197,.45)}
    .dd-track-shadow{position:absolute;top:39px;left:0;width:26px;height:6px;border-radius:50%;
        background:rgba(0,0,0,.45);filter:blur(2px);transform:translateX(-50%);
        transition:left 1.6s cubic-bezier(.22,1,.36,1)}
    .dd-track-runner{position:absolute;top:6px;left:0;transform:translateX(-50%);
        font-size:24px;transition:left 1.6s cubic-bezier(.22,1,.36,1);filter:drop-shadow(0 3px 4px rgba(0,0,0,.5))}
    .dd-track-runner.flying{animation:dd-bob 1s ease-in-out infinite}
    @keyframes dd-bob{0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(-4px)}}

    .dd-milestone{position:absolute;top:4px;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:6px;z-index:2}
    .dd-milestone-icon{font-size:16px;line-height:1;opacity:.55;transition:opacity .4s ease}
    .dd-milestone-dot{width:16px;height:16px;border-radius:50%;background:var(--dd-surface-alt);
        border:2px solid var(--dd-border-strong);transition:all .4s ease}
    .dd-milestone.reached .dd-milestone-icon{opacity:1}
    .dd-milestone.reached .dd-milestone-dot{background:var(--dd-accent);border-color:var(--dd-accent);
        box-shadow:0 0 0 4px rgba(52,245,197,.18);animation:dd-pop .5s ease}
    @keyframes dd-pop{0%{transform:scale(.6)}60%{transform:scale(1.25)}100%{transform:scale(1)}}
    .dd-milestone-label{font-size:11px;color:var(--dd-text-muted);white-space:normal;width:84px;text-align:center;line-height:1.3}
    .dd-milestone.reached .dd-milestone-label{color:var(--dd-text-sec)}

    .dd-track-foot{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
    .dd-track-msg{font-size:13px;color:var(--dd-text-sec)}
    .dd-track-msg b{color:var(--dd-accent);font-weight:600}
    .dd-badge-unlocked{display:none;align-items:center;gap:7px;background:rgba(255,176,32,.14);
        color:var(--dd-gold);border:1px solid rgba(255,176,32,.4);padding:7px 14px;border-radius:999px;
        font-size:12.5px;font-weight:600;animation:dd-badge-in .5s ease}
    .dd-track-card.is-complete .dd-badge-unlocked{display:inline-flex}
    @keyframes dd-badge-in{from{opacity:0;transform:translateY(6px) scale(.9)}to{opacity:1;transform:translateY(0) scale(1)}}
    .dd-track-card.is-complete{border-color:rgba(255,176,32,.3)}

    .dd-confetti{position:absolute;inset:0;pointer-events:none;overflow:hidden}
    .dd-confetti i{position:absolute;top:-12px;width:6px;height:10px;opacity:0;border-radius:1px;
        animation:dd-fall 2.8s ease-in forwards}
    @keyframes dd-fall{
        0%{opacity:1;transform:translateY(0) rotate(0deg)}
        100%{opacity:0;transform:translateY(280px) rotate(420deg)}
    }
    @keyframes dd-trophy-pulse{
        0%,100%{box-shadow:0 0 0 0 rgba(255,176,32,.35)}
        50%{box-shadow:0 0 0 5px rgba(255,176,32,0)}
    }
    .dd-rank.r1{animation:dd-trophy-pulse 2.2s ease-in-out infinite}

    /* Today's Top Closer Card */
    .dd-top-closer-card{display:flex;align-items:center;gap:20px;margin-top:16px;
        background:linear-gradient(135deg,rgba(52,245,197,.08) 0%,rgba(17,22,29,.95) 60%);
        border:1.5px solid rgba(52,245,197,.3);border-radius:14px;padding:18px 24px;
        position:relative;overflow:hidden;animation:dd-rise .5s ease forwards;opacity:0;transform:translateY(10px)}
    .dd-top-closer-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;
        background:linear-gradient(90deg,var(--dd-accent),var(--dd-gold),var(--dd-accent))}
    .dd-top-closer-card::after{content:'';position:absolute;top:-50%;right:-20%;width:200px;height:200px;
        background:radial-gradient(circle,rgba(52,245,197,.06),transparent 70%);pointer-events:none}
    .dd-top-closer-trophy{font-size:42px;filter:drop-shadow(0 4px 12px rgba(255,176,32,.35));
        animation:dd-trophy-bounce 2s ease-in-out infinite}
    @keyframes dd-trophy-bounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-6px) rotate(3deg)}}
    .dd-top-closer-info{flex:1;min-width:0}
    .dd-top-closer-label{font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--dd-gold);font-weight:700;margin-bottom:4px}
    .dd-top-closer-name{font-family:var(--dd-font-display);font-size:22px;font-weight:700;color:var(--dd-text);
    text-align:center}
    .dd-top-closer-divider{width:1px;align-self:stretch;background:var(--dd-border-strong);margin:0 4px}
    .dd-top-closer-team{font-size:12px;color:var(--dd-text-sec);margin-top:2px}
    .dd-top-closer-stats{display:flex;gap:16px}
    .dd-top-closer-stat{display:flex;flex-direction:column;align-items:center;gap:3px;
        background:rgba(255,255,255,.04);border:1px solid var(--dd-border);border-radius:10px;padding:10px 16px;min-width:68px}
    .dd-top-closer-stat-val{font-family:var(--dd-font-mono);font-size:20px;font-weight:600;color:var(--dd-accent)}
    .dd-top-closer-stat-lbl{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--dd-text-muted)}
    
    /* Announcement Broadcast Celebration Styles */
    #ddAnnouncementCelebration{position:fixed;inset:0;z-index:99999;pointer-events:none;display:none;overflow:hidden;
        background:
            radial-gradient(circle at 50% 46%, rgba(255,176,32,.18), rgba(9,13,18,.18) 34%, rgba(9,13,18,0) 68%),
            radial-gradient(circle at 50% 82%, rgba(52,245,197,.10), rgba(9,13,18,0) 48%);
        opacity:0;transition:opacity .5s ease}
    #ddAnnouncementCelebration.show{display:block;opacity:1}
    #ddAnnouncementCelebration.show::before{content:'';position:absolute;inset:-20%;opacity:.34;
        background:conic-gradient(from 90deg, rgba(255,176,32,0), rgba(255,176,32,.16), rgba(52,245,197,.10), rgba(255,176,32,0));
        animation:dd-ann-rotate 7s linear infinite}
    @keyframes dd-ann-rotate{to{transform:rotate(360deg)}}
    .dd-msg-banner{position:absolute;top:45%;left:50%;transform:translate(-50%,-50%) scale(.85);
        opacity:0;text-align:center;animation:dd-banner-pop .6s cubic-bezier(.22,1,.36,1) forwards,
        dd-banner-out .6s ease forwards 59s}
    .dd-msg-pill{display:inline-flex;flex-direction:column;align-items:center;gap:6px;
        background:linear-gradient(180deg,rgba(17,22,29,.96),rgba(9,13,18,.99));
        backdrop-filter:blur(18px);border:2px solid rgba(255,176,32,.58);
        border-radius:30px;padding:36px 64px 34px;box-shadow:0 30px 86px rgba(0,0,0,.82),0 0 58px rgba(255,176,32,.34), inset 0 1px 0 rgba(255,255,255,.09);
        min-width:520px;position:relative;overflow:hidden}
    .dd-msg-pill::before{content:'';position:absolute;top:0;left:-35%;width:34%;height:100%;
        background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,.16),rgba(255,255,255,0));
        transform:skewX(-18deg);animation:dd-ann-sweep 2.8s ease-in-out infinite}
    @keyframes dd-ann-sweep{0%{left:-40%}55%,100%{left:112%}}
    .dd-msg-kicker{display:inline-flex;align-items:center;gap:8px;font-size:14px;text-transform:uppercase;letter-spacing:.22em;color:var(--dd-gold);font-weight:700;margin-bottom:6px}
    .dd-msg-kicker::before{content:'';width:10px;height:10px;border-radius:50%;background:#ff3b3b;box-shadow:0 0 0 0 rgba(255,59,59,.55);animation:dd-live-dot 1.05s ease-in-out infinite}
    @keyframes dd-live-dot{70%{box-shadow:0 0 0 10px rgba(255,59,59,0)}100%{box-shadow:0 0 0 0 rgba(255,59,59,0)}}
    .dd-msg-person{display:none;flex-direction:column;align-items:center;gap:10px;margin:10px 0 8px;padding:18px 24px;border-radius:26px;
        background:rgba(255,255,255,.055);border:1px solid rgba(255,176,32,.30);position:relative}
    .dd-msg-avatar-wrap{position:relative;width:184px;height:184px;display:flex;align-items:center;justify-content:center}
    .dd-msg-avatar-ring{position:absolute;inset:0;border-radius:50%;border:1px solid rgba(255,176,32,.36);animation:dd-avatar-ring 2.2s ease-out infinite}
    .dd-msg-avatar-ring.ring-2{animation-delay:.8s;border-color:rgba(52,245,197,.28)}
    @keyframes dd-avatar-ring{0%{opacity:.9;transform:scale(.86)}100%{opacity:0;transform:scale(1.18)}}
    .dd-msg-avatar{width:168px;height:168px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,176,32,.78);
        box-shadow:0 0 28px rgba(255,176,32,.30),0 0 0 8px rgba(255,176,32,.08);background:var(--dd-surface-alt);position:relative;z-index:2}
    .dd-msg-author{font-family:var(--dd-font-display);font-size:22px;font-weight:700;color:var(--dd-text);
        line-height:1.1;text-align:center}
    .dd-msg-wave{display:flex;align-items:flex-end;gap:4px;height:28px;margin-top:8px;opacity:.9}
    .dd-msg-wave span{width:5px;border-radius:999px;background:linear-gradient(180deg,var(--dd-gold),var(--dd-accent));height:9px;animation:dd-ann-wave .88s ease-in-out infinite}
    .dd-msg-wave span:nth-child(2){animation-delay:.08s}
    .dd-msg-wave span:nth-child(3){animation-delay:.16s}
    .dd-msg-wave span:nth-child(4){animation-delay:.24s}
    .dd-msg-wave span:nth-child(5){animation-delay:.16s}
    .dd-msg-wave span:nth-child(6){animation-delay:.08s}
    .dd-msg-wave span:nth-child(7){animation-delay:.02s}
    @keyframes dd-ann-wave{0%,100%{height:8px;opacity:.5}50%{height:27px;opacity:1}}
    @media(max-width:620px){
        .dd-msg-pill{min-width:0;max-width:92vw;padding:28px 22px}
        .dd-msg-avatar-wrap{width:132px;height:132px}
        .dd-msg-avatar{width:120px;height:120px}
        .dd-msg-author{font-size:18px}
    }
    .dd-msg-text{font-family:var(--dd-font-display);font-weight:700;font-size:42px;letter-spacing:.2px;
        color:transparent;line-height:1.25;margin:12px 0 2px;max-width:860px;
        background:linear-gradient(95deg,#fff 12%,#ffcf75 38%,#34f5c5 62%,#fff 88%);
        background-size:220% 100%;-webkit-background-clip:text;background-clip:text;
        text-shadow:0 0 24px rgba(255,176,32,.24);animation:dd-ann-text-shimmer 3s linear infinite}
    @keyframes dd-ann-text-shimmer{to{background-position:-220% 0}}

    /* Team Overtake Celebration */
    #ddTeamOvertakeCelebration{position:fixed;inset:0;z-index:99999;pointer-events:none;display:none;overflow:hidden;
        background:radial-gradient(circle at 50% 55%, rgba(56,189,248,.12), rgba(9,13,18,.20) 42%, rgba(9,13,18,0) 68%);
        opacity:0;transition:opacity .45s ease}
    #ddTeamOvertakeCelebration.show{display:block;opacity:1}
    .dd-overtake-streaks{position:absolute;inset:0;overflow:hidden}
    .dd-overtake-line{position:absolute;height:3px;border-radius:999px;opacity:0;
        background:linear-gradient(90deg,rgba(255,255,255,0),var(--dd-rank-4),var(--dd-accent),rgba(255,255,255,0));
        filter:drop-shadow(0 0 10px rgba(56,189,248,.55));
        animation:dd-overtake-line 1.15s cubic-bezier(.2,.72,.26,1) forwards}
    @keyframes dd-overtake-line{
        0%{opacity:0;transform:translateX(-18vw) skewX(-16deg) scaleX(.45)}
        15%{opacity:.9}
        100%{opacity:0;transform:translateX(118vw) skewX(-16deg) scaleX(1)}
    }
    .dd-overtake-banner{position:absolute;top:46%;left:50%;transform:translate(-50%,-46%) scale(.86);
        opacity:0;text-align:center;animation:dd-overtake-pop .55s cubic-bezier(.22,1,.36,1) forwards,
        dd-overtake-out .5s ease forwards 11.4s}
    @keyframes dd-overtake-pop{
        0%{opacity:0;transform:translate(-50%,-38%) scale(.86)}
        100%{opacity:1;transform:translate(-50%,-50%) scale(1)}
    }
    @keyframes dd-overtake-out{to{opacity:0;transform:translate(-50%,-56%) scale(.96)}}
    .dd-overtake-pill{display:inline-flex;flex-direction:column;align-items:center;gap:10px;
        min-width:520px;max-width:min(860px,92vw);padding:32px 54px;border-radius:24px;
        background:linear-gradient(180deg,rgba(17,22,29,.94),rgba(9,13,18,.98));
        border:2px solid rgba(56,189,248,.42);box-shadow:0 26px 70px rgba(0,0,0,.78),0 0 46px rgba(56,189,248,.26);
        backdrop-filter:blur(16px)}
    .dd-overtake-kicker{font-size:13px;text-transform:uppercase;letter-spacing:.22em;color:var(--dd-rank-4);
        font-weight:700}
    .dd-overtake-match{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;
        font-family:var(--dd-font-display);font-size:36px;font-weight:700;line-height:1.15;color:var(--dd-text)}
    .dd-overtake-match span:first-child{color:var(--dd-accent);text-shadow:0 0 22px rgba(52,245,197,.35)}
    .dd-overtake-match span:last-child{color:var(--dd-gold);text-shadow:0 0 22px rgba(255,176,32,.30)}
    .dd-overtake-vs{font-family:var(--dd-font-body)!important;font-size:13px!important;letter-spacing:.12em;
        text-transform:uppercase;color:var(--dd-text-sec)!important;border:1px solid var(--dd-border-strong);
        border-radius:999px;padding:6px 12px;background:rgba(255,255,255,.04);text-shadow:none!important}
    .dd-overtake-sub{font-size:14px;color:var(--dd-text-sec);font-weight:600}
    .dd-overtake-sub span{color:var(--dd-rank-4);font-family:var(--dd-font-mono)}
    @media(max-width:620px){
        .dd-overtake-pill{min-width:0;padding:26px 22px}
        .dd-overtake-match{font-size:27px}
    }
</style>
