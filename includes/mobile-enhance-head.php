<style id="dan-mobile-enhance">
/* Sitewide mobile-first / interactivity polish. Purely additive — doesn't
   touch existing layout classes, just improves touch feel and adds a
   little motion so the site feels alive on a phone. */
html { -webkit-text-size-adjust: 100%; }
html, body { overflow-x: hidden; }
* { -webkit-tap-highlight-color: transparent; }
a, button, input[type="submit"], input[type="button"], [class*="btn"] { touch-action: manipulation; }

/* Comfortable tap targets on touch devices */
@media (hover: none) and (pointer: coarse) {
  button, a[class*="btn"], input[type="submit"], [class*="btn"] { min-height: 44px; }
}

/* Gentle press feedback */
[class*="btn"]:active, button:active { transform: scale(0.97); }
[class*="card"] { transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s ease, opacity .6s ease; }

/* Scroll-reveal state (JS toggles .dai-in-view) */
[class*="card"] { opacity: 0; transform: translateY(22px); }
[class*="card"].dai-in-view { opacity: 1; transform: translateY(0); }
@media (prefers-reduced-motion: reduce) {
  [class*="card"] { opacity: 1 !important; transform: none !important; transition: none !important; }
}

/* Back to top button */
.dai-top-btn {
  position: fixed; bottom: 22px; left: 22px; z-index: 9997;
  width: 46px; height: 46px; border-radius: 50%; border: none;
  background: rgba(26,42,58,0.9); color: #fff; font-size: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 6px 20px rgba(0,0,0,.25);
  opacity: 0; pointer-events: none; transform: translateY(10px);
  transition: opacity .25s ease, transform .25s ease;
}
.dai-top-btn.show { opacity: 1; pointer-events: auto; transform: translateY(0); }
@media (max-width: 640px) { .dai-top-btn { left: 16px; bottom: 16px; width: 42px; height: 42px; } }
</style>
