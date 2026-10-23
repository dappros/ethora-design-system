/* =============================================================================
   HERO CHAT DEMO  ·  home page
   -----------------------------------------------------------------------------
   Plays ONCE when the section scrolls into view, then hands the chats over: both
   composers unlock, the threads scroll, the inbox rooms are clickable and each
   keeps its own history. A small Replay sits in the colour picker row.

   Speed is fixed at 1×. The tuning sandbox (with speed controls, notes and a
   fidelity table) is www/chat-animation-lab.html — deliberately not linked from
   anywhere on the site.

   Motion lives in css/hero-chat-demo.css: every visual transition is a CSS
   transition or @keyframes. This file is only the sequencer — it decides WHEN a
   step happens, never HOW it moves — so the animation can be retimed or replaced
   without touching JS. Honours prefers-reduced-motion.

   Guard: everything below no-ops if #hcd-demo is absent, so the script is safe
   to enqueue on any page.
   ============================================================================= */
(function () {
  'use strict';

  if (!document.getElementById('demo')) return;   // section not on this page

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var chats = {
    desk:  document.getElementById('chatDesk'),
    phone: document.getElementById('chatPhone'),
    mob:   document.getElementById('chatMob')
  };
  var roomsEl  = document.getElementById('rooms');
  var picker   = document.getElementById('picker');
  var restartBt= document.getElementById('restart');
  var roleSw   = document.getElementById('roleSwitch');
  var demo     = document.getElementById('demo');

  var speed = 1;   // fixed on the landing; the lab page is where it gets tuned
  var userTouched = false;   // set on first interaction; retires auto-replay
  var mobRole = 'manager', timers = [], running = false;

  /* =========================================================================
     ROOMS
     The manager has an inbox; the phone is ONE person's device. So Emma's room
     is mirrored across both frames, and the other rooms live on the desktop
     only — which is how a support desk actually works, and it keeps clicking
     around the inbox truthful instead of pretending every contact shares the
     same phone.
     ========================================================================= */
  var ROOMS = [
    {
      id:'emma', name:'Emma Ross', initials:'EM', preview:'Order #4471',
      mirrored:true,                      // the phone shows this thread
      msgs:[],                            // filled by the intro animation
      replies:[
        { text:'Vale, lo reviso ahora mismo.', tr:"OK, I'm checking it right now." },
        { text:'Gracias, muy amable.',         tr:'Thank you, very kind.' },
        { text:'¿Puedes enviarme la factura?', tr:'Can you send me the invoice?' }
      ]
    },
    {
      id:'jonas', name:'Jonas Trent', initials:'JT', preview:'Thanks!',
      msgs:[
        { from:'peer',    text:'Morning — did the replacement part ship?', time:'11:04' },
        { from:'manager', text:'It went out yesterday, arriving Thursday.', time:'11:06' },
        { from:'peer',    text:'Thanks!', time:'11:06' }
      ],
      replies:[
        { text:'Perfect, that works for me.' },
        { text:'Could you add it to the same invoice?' },
        { text:'Appreciate the quick reply.' }
      ]
    },
    {
      id:'lena', name:'Lena Park', initials:'LP', preview:'Invoice sent',
      msgs:[
        { from:'manager', text:'Invoice #2210 is attached.',
          file:{ name:'invoice-2210.pdf', size:'112 KB' }, time:'09:31' },
        { from:'peer',    text:'Received — paying today.', time:'09:48' }
      ],
      replies:[
        { text:'Payment just went through.' },
        { text:'Can I get a receipt for accounting?' },
        { text:'All good on my side.' }
      ]
    }
  ];

  var byId = {};
  ROOMS.forEach(function (r) { r.unread = 0; r.seed = r.msgs.slice(); byId[r.id] = r; });
  var activeId = 'emma';
  function active() { return byId[activeId]; }
  function emma()   { return byId.emma; }

  /* In a room, the manager's counterpart is `customer` for Emma (she owns the
     phone) and `peer` for everyone else. */
  function peerOf(room) { return room.mirrored ? 'customer' : 'peer'; }
  function nameOf(room, who)     { return who === 'manager' ? 'Support' : room.name; }
  function initialsOf(room, who) { return who === 'manager' ? 'SP' : room.initials; }

  /* ---- theme ------------------------------------------------------------ */
  picker.addEventListener('click', function (e) {
    var b = e.target.closest('.swatch'); if (!b) return;
    /* The lab put data-theme on <body>; here the themed scope is the section
       itself (.hcd[data-theme=...]), so writing to body would have silently
       done nothing — and a colour picker that does nothing is worse than none. */
    demo.dataset.theme = b.dataset.theme;
    picker.querySelectorAll('.swatch').forEach(function (s) {
      s.setAttribute('aria-pressed', String(s === b));
    });
  });

  /* ---- the scripted opener (Emma's room) -------------------------------- */
  var SCRIPT = [
    { from:'customer', text:'Hola — mi pedido #4471 todavía no ha llegado.',
      tr:"Hi — my order #4471 still hasn't arrived.",
      time:'14:02', pause:500, typing:0 },

    { from:'manager',  text:'Checking now, Emma — one moment.',
      tr:'Lo estoy comprobando, Emma — un momento.',
      time:'14:02', pause:700, typing:900 },

    { from:'manager',  text:'It ships today. Here is your return label, just in case.',
      tr:'Se envía hoy. Aquí tienes tu etiqueta de devolución, por si acaso.',
      file:{ name:'return-label-4471.pdf', size:'248 KB' },
      time:'14:03', pause:500, typing:1200 },

    { from:'customer', text:'¡Perfecto, muchas gracias!',
      tr:'Perfect, thank you very much!',
      time:'14:03', pause:900, typing:700 }
  ];

  /* =========================================================================
     RENDERING
     ========================================================================= */
  function bodyOf(chat){ return chat.querySelector('.chat-body'); }
  function viewerOf(chat){ return chat.dataset.viewer; }

  /* Smooth scrolling looks right when a person sends something, and janks badly
     during the scripted run: each new bubble is transitioning transform+opacity
     while the container is mid-animation, and a second smooth scroll starting
     before the first finishes fights it. During the run we jump; only a message
     the visitor sent themselves animates the scroll. */
  function scrollDown(chat, smooth) {
    var b = bodyOf(chat);
    if (smooth && !reduce) {
      b.scrollTo({ top: b.scrollHeight, behavior: 'smooth' });
    } else {
      b.scrollTop = b.scrollHeight;
    }
  }

  /* Insert, then flip to .in next frame so the CSS transition has a start
     value; doing both in one tick would skip the animation entirely. */
  function mount(parent, el, instant) {
    parent.appendChild(el);
    if (instant || reduce) { el.classList.add('in'); return; }
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { el.classList.add('in'); });
    });
  }

  function dayEl() {
    var d = document.createElement('div');
    d.className = 'day'; d.textContent = 'Today';
    return d;
  }

  function fileEl(f) {
    var wrap = document.createElement('button');
    wrap.type = 'button'; wrap.className = 'file';

    var ico = document.createElement('span');
    ico.className = 'file-ico';
    ico.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>';

    var meta = document.createElement('span');
    meta.className = 'file-meta';
    var n = document.createElement('span');
    n.className = 'file-name'; n.textContent = f.name;
    var sz = document.createElement('span');
    sz.className = 'file-size'; sz.textContent = f.size;
    meta.appendChild(n); meta.appendChild(sz);

    var dl = document.createElement('span');
    dl.className = 'file-dl';
    dl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 12 5 5 5-5"/><path d="M5 21h14"/></svg>';

    wrap.appendChild(ico); wrap.appendChild(meta); wrap.appendChild(dl);
    return wrap;
  }

  /* The component's DoubleTick, copied path-for-path from assets/icons.tsx:
     16×16, viewBox 0 0 16 16, stroke #16A34A, width 1.5, round caps/joins.
     The green is hardcoded there, so it does not follow the theme — keeping
     that faithful matters more than making it match the swatch. */
  function tickEl() {
    var s = document.createElement('span');
    s.className = 'tick';
    // aria-label is only valid on elements with a role (axe aria-prohibited-attr)
    s.setAttribute('role', 'img');
    s.setAttribute('aria-label', 'Sent');
    s.innerHTML =
      '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
      '<path d="M1.75 9.75L4.25 12.25M7.75 8.25L10.25 5.75M5.75 9.75L8.25 12.25L14.25 5.75" ' +
      'stroke="#16A34A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    return s;
  }

  function rowEl(msg, viewer, room, opts) {
    opts = opts || {};
    var own = msg.from === viewer;
    var showsTr = !own && !!msg.tr;

    var row = document.createElement('div');
    row.className = 'row'
      + (own ? ' own' : '')
      + (own && opts.pending ? ' pending' : '')
      + (own && opts.settled ? ' read' : '')
      + (msg.file ? ' has-file' : '')
      + (showsTr ? ' has-tr' : '');

    if (!own) {
      var av = document.createElement('div');
      av.className = 'row-avatar';
      av.textContent = initialsOf(room, msg.from);
      row.appendChild(av);
    }

    var b = document.createElement('div');
    b.className = 'bubble';

    if (!own) {
      var s = document.createElement('span');
      s.className = 'sender'; s.textContent = nameOf(room, msg.from);
      b.appendChild(s);
    }

    if (msg.file) b.appendChild(fileEl(msg.file));

    var t = document.createElement('span');
    t.className = 'text'; t.textContent = msg.text;     // textContent, never innerHTML
    b.appendChild(t);

    /* Translation is a RECEIVER-side view: the sender sees what they wrote, the
       other side gets original + divider + translation. Same data, two
       renderings — which is how the component works. */
    if (showsTr) {
      t.classList.add('orig');
      var hr = document.createElement('div');
      hr.className = 'tr-divider';
      b.appendChild(hr);
      var tr = document.createElement('span');
      tr.className = 'text tr'; tr.textContent = msg.tr;
      b.appendChild(tr);
    }

    /* Order matches Message.tsx's CustomMessageTimestamp: 'sending...' first,
       then the time, then the DoubleTick. Both states are rendered and CSS
       shows one, so flipping pending → sent needs only a class change. */
    var time = document.createElement('span');
    time.className = 'time';
    if (own) {
      var sending = document.createElement('span');
      sending.className = 'sending'; sending.textContent = 'sending...';
      time.appendChild(sending);
    }
    time.appendChild(document.createTextNode(msg.time || nowLabel()));
    if (own) time.appendChild(tickEl());
    b.appendChild(time);
    row.appendChild(b);
    return row;
  }

  function typingEl(room, from) {
    var row = document.createElement('div');
    row.className = 'row';
    var av = document.createElement('div');
    av.className = 'row-avatar'; av.textContent = initialsOf(room, from);
    var b = document.createElement('div');
    b.className = 'bubble';
    b.innerHTML = '<div class="typing" role="img" aria-label="Typing"><i></i><i></i><i></i></div>';
    row.appendChild(av); row.appendChild(b);
    return row;
  }

  function nowLabel() {
    var d = new Date();
    return String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
  }

  /* Repaint a whole thread with no entrance animation — used when switching
     rooms, where a staggered replay of old history would read as new mail. */
  function paint(chat, room, viewer) {
    var body = bodyOf(chat);
    body.textContent = '';
    if (!room.msgs.length) return;
    mount(body, dayEl(), true);
    room.msgs.forEach(function (m) {
      mount(body, rowEl(m, viewer, room, { settled:true }), true);
    });
    body.scrollTop = body.scrollHeight;
  }

  /* ---- inbox ------------------------------------------------------------ */
  function lastPreview(room) {
    if (!room.msgs.length) return room.preview;
    var m = room.msgs[room.msgs.length - 1];
    return m.file ? m.file.name : m.text;
  }

  /* The inbox is built ONCE and then updated in place.
     It used to be torn down and rebuilt on every delivered message — three
     buttons destroyed and recreated mid-animation, which forced a layout pass
     on the whole panel each time and restarted the badge animation from
     scratch. That was the single biggest source of the stutter. */
  var roomEls = {};

  function buildRooms() {
    ROOMS.forEach(function (room) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'room';
      b.dataset.room = room.id;

      var av = document.createElement('span');
      av.className = 'room-av'; av.textContent = room.initials;

      var meta = document.createElement('span');
      meta.className = 'room-meta';
      var n = document.createElement('span');
      n.className = 'room-name'; n.textContent = room.name;
      var l = document.createElement('span');
      l.className = 'room-last';
      meta.appendChild(n); meta.appendChild(l);

      var badge = document.createElement('span');
      badge.className = 'badge';

      b.appendChild(av); b.appendChild(meta); b.appendChild(badge);
      roomsEl.appendChild(b);

      roomEls[room.id] = { btn:b, last:l, badge:badge };
    });
  }

  function syncRooms() {
    ROOMS.forEach(function (room) {
      var el = roomEls[room.id];
      if (!el) return;

      var isActive = room.id === activeId;
      el.btn.classList.toggle('active', isActive);
      el.btn.setAttribute('aria-current', String(isActive));

      var preview = lastPreview(room);
      if (el.last.textContent !== preview) el.last.textContent = preview;

      if (room.unread) {
        if (el.badge.textContent !== String(room.unread)) {
          el.badge.textContent = String(room.unread);
        }
        el.badge.classList.add('show');       // idempotent: won't restart the pop
      } else {
        el.badge.classList.remove('show');
        el.badge.textContent = '';
      }
    });
  }

  function openRoom(id) {
    activeId = id;
    var room = active();
    room.unread = 0;

    document.getElementById('deskTitle').textContent = room.name;
    document.getElementById('deskAv').textContent    = room.initials;

    paint(chats.desk, room, 'manager');
    syncRooms();

    // the mobile surface follows the desktop selection while showing the manager
    if (chats.mob && mobRole === 'manager') {
      document.getElementById('mobTitle').textContent = room.name;
      document.getElementById('mobAv').textContent    = room.initials;
      paint(chats.mob, room, 'manager');
    }
  }

  roomsEl.addEventListener('click', function (e) {
    var b = e.target.closest('.room'); if (!b || running) return;
    userTouched = true;
    openRoom(b.dataset.room);
  });

  /* =========================================================================
     DELIVERY — one message into one room, across whichever frames show it
     ========================================================================= */
  function surfacesFor(room) {
    var list = [];
    if (room.id === activeId) list.push(chats.desk);
    if (room.mirrored) list.push(chats.phone);
    if (chats.mob) {
      if (mobRole === 'manager'  && room.id === activeId) list.push(chats.mob);
      if (mobRole === 'customer' && room.mirrored)        list.push(chats.mob);
    }
    return list;
  }

  function ensureDay(chat) {
    var body = bodyOf(chat);
    if (!body.querySelector('.day')) mount(body, dayEl());
  }

  function fireSend(chat) {
    var btn = chat.querySelector('.send');
    btn.classList.add('fire');
    setTimeout(function () { btn.classList.remove('fire'); }, 420);
  }

  function flash(chat) {
    if (reduce) return;
    chat.classList.add('arrive');
    setTimeout(function () { chat.classList.remove('arrive'); }, 720);
  }

  function deliver(msg, room, smooth) {
    room.msgs.push(msg);
    var senderRows = [];
    var frames = surfacesFor(room);

    // 1. the sender's own frame gets it immediately, marked pending.
    //    The composer empties HERE, at the moment of send — clearing it on a
    //    separate timer raced the delivery and could leave the typed text
    //    sitting in the input after the run finished.
    frames.forEach(function (chat) {
      if (viewerOf(chat) !== msg.from) return;
      ensureDay(chat);
      chat.querySelector('.chat-input').value = '';
      var row = rowEl(msg, viewerOf(chat), room, { pending:true });
      mount(bodyOf(chat), row);
      scrollDown(chat, smooth);
      senderRows.push(row);
      fireSend(chat);
    });

    // 2. the other frame receives it a beat later — that beat IS the demo
    at(340, function () {
      frames.forEach(function (chat) {
        if (viewerOf(chat) === msg.from) return;
        ensureDay(chat);
        mount(bodyOf(chat), rowEl(msg, viewerOf(chat), room));
        scrollDown(chat, smooth);
        flash(chat);
      });

      // an inbox entry the manager isn't looking at earns an unread badge
      if (msg.from !== 'manager' && room.id !== activeId) room.unread++;
      syncRooms();

      // 3. only now is the receipt honest
      at(260, function () {
        senderRows.forEach(function (r) { r.classList.remove('pending'); r.classList.add('read'); });
      });
    });
  }

  /* ---- helpers ---------------------------------------------------------- */
  function at(ms, fn) { var t = setTimeout(fn, ms / speed); timers.push(t); return t; }
  function clearAll() { timers.forEach(clearTimeout); timers = []; stopTypers(); }

  /* One rAF loop that writes whatever character the clock says it is at, rather
     than one setTimeout per character. The old version queued ~56 timers per
     message on each surface — around 170 timers firing every 18ms, each one a
     separate style recalc, which is exactly the kind of thing that makes an
     animation feel jittery. This writes at most once per frame and skips
     straight to the right character if a frame is dropped. */
  var typers = [];

  function typeInto(chat, text, ms) {
    var input = chat.querySelector('.chat-input');
    var start = null;
    var last = -1;
    var cancelled = false;

    input.value = '';

    function frame(now) {
      if (cancelled) return;
      if (start === null) start = now;
      var progress = Math.min(1, (now - start) / (ms / speed));
      var chars = Math.round(progress * text.length);
      if (chars !== last) {          // only touch the DOM when it changes
        input.value = text.slice(0, chars);
        last = chars;
      }
      if (progress < 1) requestAnimationFrame(frame);
    }

    requestAnimationFrame(frame);
    typers.push(function () { cancelled = true; });
  }

  function stopTypers() { typers.forEach(function (f) { f(); }); typers = []; }

  /* =========================================================================
     THE ONE-SHOT OPENER
     ========================================================================= */
  function reset() {
    clearAll();
    running = false;
    restartBt.classList.remove('show');
    activeId = 'emma';
    ROOMS.forEach(function (r) { r.unread = 0; r.msgs = r.seed.slice(); });
    emma().msgs = [];                       // Emma's thread is written live

    document.getElementById('deskTitle').textContent = emma().name;
    document.getElementById('deskAv').textContent    = emma().initials;

    [chats.desk, chats.phone, chats.mob].forEach(function (chat) {
      if (!chat) return;
      bodyOf(chat).textContent = '';
      var input = chat.querySelector('.chat-input');
      input.value = ''; input.disabled = true;
      chat.querySelector('.send').disabled = true;
    });
    syncRooms();
  }

  function unlock() {
    running = false;
    restartBt.classList.add('show');
    [chats.desk, chats.phone, chats.mob].forEach(function (chat) {
      if (!chat) return;
      var input = chat.querySelector('.chat-input');
      input.value = '';                    // never hand over a pre-filled composer
      input.disabled = false;
      chat.querySelector('.send').disabled = false;
    });
    chats.desk.querySelector('.chat-input').placeholder = 'Reply — or open another chat on the left';
    chats.phone.querySelector('.chat-input').placeholder = 'Type a message';
    if (chats.mob) chats.mob.querySelector('.chat-input').placeholder = 'Type a message';
  }

  function play() {
    reset();
    running = true;

    var room = emma();
    var clock = 400;

    SCRIPT.forEach(function (msg) {
      clock += msg.pause;

      if (msg.typing) {
        var holders = [];
        var showAt = clock;
        at(showAt, function () {
          surfacesFor(room).forEach(function (chat) {
            if (viewerOf(chat) === msg.from) return;
            ensureDay(chat);
            var el = typingEl(room, msg.from);
            mount(bodyOf(chat), el);
            scrollDown(chat);
            holders.push(el);
          });
        });
        // meanwhile the SENDER types into their own composer
        at(showAt, function () {
          surfacesFor(room).forEach(function (chat) {
            if (viewerOf(chat) !== msg.from) return;
            typeInto(chat, msg.text, msg.typing * 0.8);
          });
        });
        clock += msg.typing;
        // the indicator is REPLACED by the message, never left stacked under it
        at(clock, function () { holders.forEach(function (el) { el.remove(); }); });
      }

      at(clock, function () { deliver(msg, room); });
      clock += 700;
    });

    at(clock + 600, unlock);
  }

  /* =========================================================================
     INTERACTIVE
     ========================================================================= */
  var replyIdx = {};

  function autoReply(room) {
    var pool = room.replies || [];
    if (!pool.length) return;
    var i = (replyIdx[room.id] || 0) % pool.length;
    replyIdx[room.id] = i + 1;
    var canned = pool[i];
    var from = peerOf(room);
    var holders = [];

    at(450, function () {
      surfacesFor(room).forEach(function (chat) {
        if (viewerOf(chat) === from) return;
        ensureDay(chat);
        var el = typingEl(room, from);
        mount(bodyOf(chat), el);
        scrollDown(chat);
        holders.push(el);
      });
    });

    at(1400, function () {
      holders.forEach(function (el) { el.remove(); });
      deliver({ from: from, text: canned.text, tr: canned.tr, time: nowLabel() }, room);
    });
  }

  [chats.desk, chats.phone, chats.mob].forEach(function (chat) {
    if (!chat) return;
    var input = chat.querySelector('.chat-input');
    var send  = chat.querySelector('.send');

    function submit() {
      var v = input.value.trim();
      if (!v || running) return;
      userTouched = true;
      input.value = '';

      // the phone always speaks for Emma; the desktop speaks into whichever
      // room is open
      var room = (chat === chats.phone) ? emma() : active();
      var from = viewerOf(chat) === 'customer' ? peerOf(room) : 'manager';

      deliver({ from: from, text: v, time: nowLabel() }, room, true);
      if (from === 'manager') autoReply(room);
    }

    send.addEventListener('click', submit);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') submit(); });
  });

  /* ---- mobile role switch ----------------------------------------------- */
  roleSw.addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    mobRole = b.dataset.role;
    roleSw.querySelectorAll('button').forEach(function (x) {
      x.setAttribute('aria-pressed', String(x.dataset.role === mobRole));
    });
    chats.mob.dataset.viewer = mobRole;
    var room = mobRole === 'customer' ? emma() : active();
    document.getElementById('mobRole').textContent  = mobRole === 'manager' ? 'Manager' : 'Customer';
    document.getElementById('mobTitle').textContent = mobRole === 'manager' ? room.name : 'Support';
    document.getElementById('mobAv').textContent    = mobRole === 'manager' ? room.initials : 'SP';
    paint(chats.mob, room, mobRole);
  });

  /* ---- controls ---------------------------------------------------------- */
  restartBt.addEventListener('click', play);

  /* ---- scroll behaviour ---------------------------------------------------
     Plays when the section comes into view, and plays AGAIN each time it is
     scrolled back to — a visitor who scrolls down the page and returns should
     see the demo, not a finished transcript. It is not a loop: while the
     section stays on screen it runs exactly once.

     The one thing that must never be wiped is a conversation the visitor typed
     themselves, so any interaction (sending, or opening another room) retires
     the auto-replay for good. From then on only the Replay button restarts it.
     -------------------------------------------------------------------------- */
  buildRooms();
  syncRooms();

  if ('IntersectionObserver' in window) {
    var wasVisible = false;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          if (!wasVisible && !userTouched && !running) play();
          wasVisible = true;
        } else {
          wasVisible = false;      // left the viewport: arm the next entry
        }
      });
    }, { threshold: 0.35 });
    io.observe(demo);
  } else {
    play();
  }
})();

/* ---------------------------------------------------------------------------
   Scroll parallax: the two devices drift slightly apart as the hero scrolls
   away and settle back together on the way up. Driven by rAF off a passive
   scroll listener; animates the independent `translate` property so it cannot
   fight any transform the devices carry. Reduced-motion users get none of it.
   --------------------------------------------------------------------------- */
(function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
  var demo = document.querySelector('.hcd.hero-demo');
  if (!demo) { return; }
  var desk = demo.querySelector('.device-desktop');
  var phone = demo.querySelector('.hcd-phone-wrap');
  if (!desk || !phone) { return; }
  var ticking = false;
  function apply() {
    ticking = false;
    var r = demo.getBoundingClientRect();
    var vh = window.innerHeight || 1;
    /* 0 → demo entering from the bottom, 1 → demo leaving past the top */
    var p = (vh - r.top) / (vh + r.height);
    p = Math.max(0, Math.min(1, p));
    var drift = (p - 0.5) * 2;   /* -1 … 1, 0 with the demo centred in the viewport */
    phone.style.translate = (drift * 26).toFixed(1) + 'px 0px';
    desk.style.translate  = (drift * -14).toFixed(1) + 'px 0px';
  }
  function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(apply); } }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  apply();
})();
