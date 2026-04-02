// Simple image zoom modal for CISC3003 Pair Assignment pages.
// Click any <img> on the page to open it in a larger overlay.
(function () {
  const STYLE_ID = "pz-style";
  const MODAL_ID = "pz-modal";

  function injectStyles() {
    if (document.getElementById(STYLE_ID)) return;
    const style = document.createElement("style");
    style.id = STYLE_ID;
    style.textContent = `
      .pz-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 1.25rem;
      }
      .pz-modal.pz-open { display: flex; }
      .pz-modal img {
        max-width: 95vw;
        max-height: 92vh;
        border-radius: 12px;
        box-shadow: 0 20px 80px rgba(0,0,0,0.6);
      }
      .pz-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.35);
        background: rgba(0,0,0,0.35);
        color: #fff;
        font-size: 26px;
        line-height: 40px;
        text-align: center;
        cursor: pointer;
        user-select: none;
      }
      .pz-caption {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 1.1rem;
        color: rgba(255,255,255,0.92);
        font-size: 0.95rem;
        background: rgba(0,0,0,0.35);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        max-width: 92vw;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
    `;
    document.head.appendChild(style);
  }

  function ensureModal() {
    let modal = document.getElementById(MODAL_ID);
    if (modal) return modal;

    modal = document.createElement("div");
    modal.id = MODAL_ID;
    modal.className = "pz-modal";

    const closeBtn = document.createElement("button");
    closeBtn.className = "pz-close";
    closeBtn.type = "button";
    closeBtn.setAttribute("aria-label", "Close");
    closeBtn.textContent = "×";

    const img = document.createElement("img");
    img.alt = "";

    const caption = document.createElement("div");
    caption.className = "pz-caption";

    modal.appendChild(closeBtn);
    modal.appendChild(img);
    modal.appendChild(caption);
    document.body.appendChild(modal);

    closeBtn.addEventListener("click", close);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) close();
    });

    return modal;
  }

  let currentImgEl = null;
  function openFromImg(imgEl) {
    const modal = ensureModal();
    injectStyles();

    const modalImg = modal.querySelector("img");
    const caption = modal.querySelector(".pz-caption");

    modalImg.src = imgEl.currentSrc || imgEl.src;
    modalImg.alt = imgEl.alt || "";
    caption.textContent = imgEl.alt ? imgEl.alt : "";

    modal.classList.add("pz-open");
    modal.setAttribute("aria-hidden", "false");
    currentImgEl = imgEl;
  }

  function close() {
    const modal = document.getElementById(MODAL_ID);
    if (!modal) return;
    modal.classList.remove("pz-open");
    modal.setAttribute("aria-hidden", "true");
    currentImgEl = null;
  }

  // Close on Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") close();
  });

  // Event delegation: click any image -> open modal
  document.addEventListener("click", (e) => {
    const t = e.target;
    if (!t || !(t instanceof HTMLImageElement)) return;
    if (!t.src) return;
    e.preventDefault();
    e.stopPropagation();
    openFromImg(t);
  }, true);
})();

