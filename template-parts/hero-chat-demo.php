<?php
/**
 * Hero chat demo (the `hero-demo hcd hcd-pad` block, #demo).
 * Manager (web) and Customer (mobile) hold one conversation; plays once on
 * scroll-in, then both chats go live. Styles: css/hero-chat-demo.css,
 * behaviour: js/hero-chat-demo.js (enqueued for the home layout in functions.php).
 * Used by page-home-v2.php in two places: inside the classic hero (/home-v2/) and
 * in the stand-alone "See it live" section (front page). index.php keeps its own copy.
 */
?>
          <!-- ============================================================
               HERO CHAT DEMO
               Manager (web) and Customer (mobile) hold one conversation; a
               message sent on one device lands on the other a beat later.
               Plays once on scroll-in, then both chats go live — the inbox
               rooms are clickable and each keeps its own history.

               Styles: css/hero-chat-demo.css   Behaviour: js/hero-chat-demo.js
               Everything is scoped under `.hcd`. The tuning sandbox lives at
               www/chat-animation-lab.html and is not linked from the site.
               ============================================================ -->
          <div class="hero-demo hcd hcd-pad" id="demo" data-theme="blue">

            <div class="hcd-topbar">
              <span class="pill">Web</span>
              <span class="pill">RN / Swift / Kotlin / Flutter</span>
              <div class="picker" id="picker" role="group" aria-label="Pick colour">
              <span class="options-title">Pick colour</span>
              <button class="swatch" type="button" data-theme="blue"   aria-pressed="true"  aria-label="Blue"></button>
              <button class="swatch" type="button" data-theme="orange" aria-pressed="false" aria-label="Orange"></button>
              <button class="swatch" type="button" data-theme="purple" aria-pressed="false" aria-label="Purple"></button>
              <button class="swatch" type="button" data-theme="green"  aria-pressed="false" aria-label="Green"></button>

              <button class="restart" type="button" id="restart">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/>
                </svg>
                Replay
              </button>

              <span class="toggle role-switch" id="roleSwitch">
                <button type="button" data-role="manager" aria-pressed="true">Manager</button>
                <button type="button" data-role="customer" aria-pressed="false">Customer</button>
              </span>
            </div>
            </div><!-- /hcd-topbar -->

            <div class="hcd-devices">

              <!-- ---------- DESKTOP · MANAGER ---------- -->
              <div class="device-desktop">
                <div class="role"><b>Manager</b></div>
                <div class="browser">
                  <div class="browser-bar">
                    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                    <span class="url">app.chat.ethora.com</span>
                  </div>
                  <div class="desk">
                    <aside class="rooms" id="rooms">
                      <div class="rooms-title">Inbox</div>
                    </aside>

                    <div class="chat" id="chatDesk" data-viewer="manager">
                      <div class="chat-header">
                        <div class="chat-avatar" id="deskAv">EM</div>
                        <div>
                          <div class="chat-title" id="deskTitle">Emma Ross</div>
                          <div class="chat-sub"><span class="presence"></span>online</div>
                        </div>
                        <div class="chat-actions">
                          <button class="icon-btn" type="button" aria-label="Chat language">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                          </button>
                          <button class="icon-btn" type="button" aria-label="Audio call">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 2h2.2c.5 0 .9.3 1 .8l.9 3.4c.1.4 0 .8-.4 1.1L8.7 8.6a13 13 0 0 0 6.7 6.7l1.3-1.6c.3-.3.7-.5 1.1-.4l3.4.9c.5.1.8.5.8 1v2.2c0 1.4-1.2 2.6-2.6 2.5A18.4 18.4 0 0 1 2.1 4.6C2 3.2 3.2 2 4.6 2h2z"/></svg>
                          </button>
                          <button class="icon-btn" type="button" aria-label="Video call">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="2" y="6" width="13" height="12" rx="3"/><path d="M15 11l6-3.5v9L15 13v-2z"/></svg>
                          </button>
                        </div>
                      </div>
                      <div class="chat-body"></div>
                      <div class="chat-dock">
                        <input class="chat-input" type="text" placeholder="Type message" aria-label="Type message" disabled>
                        <button class="send" type="button" aria-label="Send" disabled>
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 20.5v-6l8-2.5-8-2.5v-6l19 8.5-19 8.5z"/></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ---------- PHONE · CUSTOMER ---------- -->
              <div class="device-phone hcd-phone-wrap">
                <div class="role"><b>Customer</b></div>
                <div class="phone">
                  <span class="side-btn sb-action" aria-hidden="true"></span>
                  <span class="side-btn sb-volup" aria-hidden="true"></span>
                  <span class="side-btn sb-voldn" aria-hidden="true"></span>
                  <span class="side-btn sb-power" aria-hidden="true"></span>
                  <div class="phone-screen">
                    <div class="hcd-statusbar" aria-hidden="true">
                      <img src="<?php echo get_template_directory_uri(); ?>/images/vector/iphone/time.svg" alt="" width="31" height="13" class="sbar-time" />
                      <span class="notch"><span class="cam"></span></span>
                      <img src="<?php echo get_template_directory_uri(); ?>/images/vector/iphone/right-side.svg" alt="" width="79" height="13" class="sbar-icons" />
                    </div>
                    <div class="chat" id="chatPhone" data-viewer="customer">
                      <div class="chat-header">
                        <div class="chat-avatar">SP</div>
                        <div>
                          <div class="chat-title">Support</div>
                          <div class="chat-sub"><span class="presence"></span>online</div>
                        </div>
                        <div class="chat-actions">
                          <button class="icon-btn" type="button" aria-label="Chat language">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                          </button>
                          <button class="icon-btn" type="button" aria-label="Video call">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="2" y="6" width="13" height="12" rx="3"/><path d="M15 11l6-3.5v9L15 13v-2z"/></svg>
                          </button>
                        </div>
                      </div>
                      <div class="chat-body"></div>
                      <div class="chat-dock">
                        <input class="chat-input" type="text" placeholder="Message" aria-label="Message" disabled>
                        <button class="send" type="button" aria-label="Send" disabled>
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 20.5v-6l8-2.5-8-2.5v-6l19 8.5-19 8.5z"/></svg>
                        </button>
                      </div>
                    </div>
                  <span class="home-indicator" aria-hidden="true"></span>
                  </div>
                </div>
              </div>

              <!-- ---------- NARROW VIEWPORT · one chat, role switch ----------
                   Two shrunken devices on a 375px screen are unreadable, and an
                   unreadable demo converts worse than none. One full-width chat
                   plus the Manager/Customer switch keeps the cross-platform
                   story and makes the tap itself the engagement. -->
              <div class="device-mobile">
                <div class="role"><b id="mobRole">Manager</b><span>· same conversation, either side</span></div>
                <div class="chat" id="chatMob" data-viewer="manager">
                  <div class="chat-header">
                    <div class="chat-avatar" id="mobAv">EM</div>
                    <div>
                      <div class="chat-title" id="mobTitle">Emma Ross</div>
                      <div class="chat-sub"><span class="presence"></span>online</div>
                    </div>
                    <div class="chat-actions">
                      <button class="icon-btn" type="button" aria-label="Chat language">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                      </button>
                      <button class="icon-btn" type="button" aria-label="Video call">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="2" y="6" width="13" height="12" rx="3"/><path d="M15 11l6-3.5v9L15 13v-2z"/></svg>
                      </button>
                    </div>
                  </div>
                  <div class="chat-body"></div>
                  <div class="chat-dock">
                    <input class="chat-input" type="text" placeholder="Type message" aria-label="Type message" disabled>
                    <button class="send" type="button" aria-label="Send" disabled>
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 20.5v-6l8-2.5-8-2.5v-6l19 8.5-19 8.5z"/></svg>
                    </button>
                  </div>
                </div>
              </div>

            </div><!-- /hcd-devices -->
          </div><!-- /hero-demo -->
