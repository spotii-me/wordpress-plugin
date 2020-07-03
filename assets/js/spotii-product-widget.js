var isProduction = true; // set this to true before moving to production

/*
 * Set URLs based on environment
 */
function setUrls() {
  var learnMoreBtn = document.getElementById("spotii-popup__button");
  var tncBtn = document.getElementById("spotii-popup__terms");
  learnMoreBtn.href = isProduction
    ? "https://spotii.me/how-it-works.html"
    : "https://staging.spotii.me/how-it-works.html";
  tncBtn.href = isProduction
    ? "https://spotii.me/terms-and-conditions.html"
    : "https://staging.spotii.me/terms-and-conditions.html";
}

var container, backdrop, styles, wrap, closeBtn, content;
var POPUP_CSS =
  ".spotii-popup{position:fixed;left:0;top:0;bottom:0;right:0;z-index:99998}.spotii-popup__backdrop{position:absolute;left:0;top:0;bottom:0;right:0;background:rgba(0,0,0,.5)}.spotii-popup__wrap{position:absolute;left:50%;top:50%;transform:translate3d(-50%,-50%,0);width:100%;max-width:754px}.spotii-popup__content{color:#333;font-size:16px;text-align:center;box-sizing:border-box;background:#fff;border-radius:16px;padding:40px 32px 24px;box-shadow:0 4px 15px rgba(0,0,0,.15)}.spotii-popup__close{color:#666;position:absolute;right:24px;top:24px;cursor:pointer}.spotii-popup__close:hover{color:#333}.spotii-popup__logo{width:160px;margin:0 auto 16px}.spotii-popup__moto{font-weight:700;font-size:1.25em;margin:0 0 8px}.spotii-popup__moto b{color:#ff4d4a}.spotii-popup__statement{color:#666;margin:0 0 40px}.spotii-popup__row{overflow:hidden;text-align:center}.spotii-popup__col{display:inline-block;vertical-align:top;width:230px;box-sizing:border-box;padding-left:12px;padding-right:12px;margin-bottom:40px}.spotii-popup__icon{color:#ff4d4a;display:inline-block;vertical-align:middle;padding:20px;background:#066;background:rgba(255,77,74,.1);border-radius:50%;margin-bottom:16px}.spotii-popup__icon svg{vertical-align:middle}.spotii-popup__point-title{font-weight:700;font-size:1em;line-height:1.375;min-height:2.75em;margin:0 0 8px}.spotii-popup__point-text{color:#666;line-height:1.5;font-size:.875em;margin:0}.spotii-popup__cta{margin:0 0 40px}.spotii-popup .spotii-popup__button,.spotii-popup .spotii-popup__button:active,.spotii-popup .spotii-popup__button:hover,.spotii-popup .spotii-popup__button:visited{display:inline-block;vertical-align:middle;color:#fff;font-weight:700;font-size:.875em;text-align:center;text-decoration:none;padding:12px 40px;border-radius:8px;background:#fe6b5f;cursor:pointer;transition:color 180ms ease,background 180ms ease}.spotii-popup .spotii-popup__button:hover{background:#ff4d4a}.spotii-popup__footer{color:#999;font-size:.75em;margin:32px 0 0}.spotii-popup__footer a{color:#999}.spotii-animation-zoom-in{opacity:0;transform:scale(.9);transition:all .3s ease}.spotii-animation-zoom-in-enter{opacity:1;transform:scale(1)}@media (max-width:768px){.spotii-popup__wrap{position:relative;left:0;top:0;height:100%;transform:none;border-radius:0;overflow-y:auto}.spotii-popup__content{min-height:100%}}";
var POPUP_CONTENT =
  '<div class="spotii-popup__inner"> <div class="spotii-popup__logo"> <svg viewBox="0 0 575 156" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <path d="M93.4,42.68 L116.74,19.34 C102.292199,4.90544225 81.9818341,-2.03751498 61.7212349,0.532217389 C41.4606357,3.10194976 23.5267068,14.8955829 13.14,32.48 C39.0890855,17.1797853 72.1029078,21.3754119 93.4,42.68 Z" fill="#FFC4BE"></path> <path d="M23.33,112.75 L0,136.08 C14.4513957,150.506892 34.7594279,157.444629 55.0171766,154.875258 C75.2749252,152.305887 93.2078982,140.517891 103.6,122.94 C77.648524,138.237056 44.6360832,134.04624 23.33,112.75 Z" fill="#FF4B44"></path> <path d="M93.4,42.68 L23.33,112.75 C44.6360832,134.04624 77.648524,138.237056 103.6,122.94 C118.900215,96.9909145 114.704588,63.9770922 93.4,42.68 Z" fill="#FFC4BE"></path> <path d="M23.33,112.75 L93.4,42.68 C72.1029078,21.3754119 39.0890855,17.1797853 13.14,32.48 C-2.1570557,58.431476 2.03375993,91.4439168 23.33,112.75 Z" fill="#FF4B44"></path> <path d="M228,94.14 C228,108.71 216.85,122.94 194,122.94 C167.25,122.94 158.68,105.63 158,96.2 L180.12,92.2 C180.47,98.03 184.58,103.69 193.49,103.69 C200.18,103.69 203.44,100.09 203.44,96.32 C203.44,93.23 201.38,90.66 195.04,89.32 L185.27,87.09 C167.09,83.15 159.89,72.86 159.89,60.86 C159.89,45.26 173.61,32.57 192.64,32.57 C217.33,32.59 225.9,48 226.76,58 L205.15,61.95 C204.47,56.29 200.87,51.49 192.98,51.49 C187.98,51.49 183.72,54.4 183.72,58.86 C183.72,62.46 186.64,64.52 190.41,65.2 L201.72,67.43 C219.39,71 228,81.62 228,94.14 Z M425.84,77.72 C425.84,102.699839 405.589839,122.95 380.61,122.95 C355.630161,122.95 335.38,102.699839 335.38,77.72 C335.38,52.7401608 355.630161,32.49 380.61,32.49 C392.606555,32.4873468 404.112524,37.2517846 412.59537,45.7346302 C421.078215,54.2174759 425.842653,65.723445 425.84,77.72 Z M399.84,77.72 C399.84,65.99 391.24,56.49 380.64,56.49 C370.04,56.49 361.4,66 361.4,77.72 C361.4,89.44 370,98.94 380.61,98.94 C391.22,98.94 399.81,89.44 399.81,77.72 L399.84,77.72 Z M518.92,0 C511.740298,-4.39629938e-16 505.92,5.82029825 505.92,13 C505.92,20.1797017 511.740298,26 518.92,26 C526.099702,26 531.92,20.1797017 531.92,13 C531.92,9.55218563 530.550361,6.24558476 528.112388,3.80761184 C525.674415,1.36963892 522.367814,2.11117741e-16 518.92,0 Z M505.92,122.94 L532,122.94 L532,46.8 L505.89,32.48 L505.92,122.94 Z M561.94,49.7 C569.119702,49.7 574.94,43.8797017 574.94,36.7 C574.94,29.5202983 569.119702,23.7 561.94,23.7 C554.760298,23.7 548.94,29.5202983 548.94,36.7 C548.94,43.8797017 554.760298,49.7 561.94,49.7 Z M548.94,56.13 L548.94,122.94 L575,122.94 L575,70.45 L548.94,56.13 Z M447.18,32.48 L431.49,32.48 L431.49,58.64 L447.18,58.64 L447.18,94.21 C447.18,101.831403 450.208274,109.140506 455.598357,114.528714 C460.988441,119.916922 468.298598,122.942653 475.92,122.94 L488.92,122.940002 L488.92,96.88 L485.43,96.88 C478.69,96.88 473.23,90.88 473.23,83.4 L473.23,58.64 L488.92,58.64 L488.92,32.48 L473.24,32.48 L473.24,14.53 L447.1,0 L447.18,32.48 Z M265.33,115.93 L265.33,155.42 L239.26,141.1 L239.26,32.48 L265.33,32.48 L265.33,39.48 C271.920131,34.9262107 279.739582,32.4848123 287.75,32.48 C310.93,32.48 329.75,52.73 329.75,77.71 C329.75,102.69 310.96,122.94 287.75,122.94 C279.739292,122.927202 271.921215,120.482745 265.33,115.93 Z M265.33,78.45 C265.69,89.83 274.12,98.93 284.49,98.93 C295.1,98.93 303.7,89.43 303.7,77.71 C303.7,65.99 295.1,56.48 284.49,56.48 C274.12,56.48 265.69,65.59 265.33,76.96 L265.33,78.45 Z" fill="#FF4B44"></path> </svg> </div><p class="spotii-popup__moto"><b>Shop</b> now. <b>Live</b> now. <b>Pay</b> later.</p><p class="spotii-popup__statement">Spread your purchase over 4 cost-free instalments</p><div class="spotii-popup__row"><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></i> <p class="spotii-popup__point-title">Quick and easy sign-up</p><p class="spotii-popup__point-text">Simply select Spotii at check-out and fill in a few pieces of basic information.</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 19 22 12 13 5 13 19"></polygon><polygon points="2 19 11 12 2 5 2 19"></polygon></svg></i> <p class="spotii-popup__point-title">Instant approval and order completion</p><p class="spotii-popup__point-text">No extra wait! Your order ships right away without the immediate hit to your wallet.</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> </i> <p class="spotii-popup__point-title">No more fees!</p><p class="spotii-popup__point-text">No interest. No hidden fees. No surprises. Pay nothing extra when you pay on time.</p></div></div><div class="spotii-popup__cta"> <a class="spotii-popup__button" id="spotii-popup__button" href="#" target="_black" rel="nofollow noopener">Learn more</a> </div><div class="spotii-popup__footer"> * Applicable for purchases over AED 200. You must be over 18, a resident of the U.A.E and meet additional eligibility criteria to qualify. Late fees may apply. Estimated payment amounts shown on product pages exclude taxes and shipping charges, which are added at checkout. <a id="spotii-popup__terms" href="#" target="_black" rel="nofollow noopener">Click here</a> for complete terms and conditions. </div><div class="spotii-popup__close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div></div>';

/*
 * Calling this method renders the Spotii popup
 */
function render() {
  container = document.createElement("div");
  container.className = "spotii-popup";
  container.id = "spotii-popup__container";

  backdrop = document.createElement("div");
  backdrop.className = "spotii-popup__backdrop";
  backdrop.addEventListener("click", hide, false);
  container.appendChild(backdrop);

  wrap = document.createElement("div");
  wrap.className = "spotii-popup__wrap";
  container.appendChild(wrap);

  content = renderContent();
  wrap.appendChild(content);

  closeBtn = renderCloseButton();
  closeBtn.addEventListener("click", hide, false);
  content.appendChild(closeBtn);

  styles = document.createElement("style");
  styles.innerHTML = POPUP_CSS;
  document.body.appendChild(styles);

  document.body.appendChild(container);

  // Set urls based on environment
  setUrls();

  show();
}

function renderContent() {
  var content = document.createElement("div");
  content.id = "spotii-popup__content";
  content.className = "spotii-popup__content";
  content.innerHTML = POPUP_CONTENT;

  return content;
}

function renderCloseButton() {
  var button = document.createElement("div");
  button.className = "spotii-popup__close";
  button.innerHTML =
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

  return button;
}

function show() {
  var content = document.getElementById("spotii-popup__content");
  content.classList.add("spotii-animation-zoom-in");
  var container = document.getElementById("spotii-popup__container");
  container.style.display = "block";
  window.requestAnimationFrame(function () {
    content.classList.add("spotii-animation-zoom-in-enter");
  });
  document.addEventListener("keydown", onDocumentKeyDown, false);
}

function hide() {
  var content = document.getElementById("spotii-popup__content");
  content.classList.remove(
    "spotii-animation-zoom-in",
    "spotii-animation-zoom-in-enter"
  );
  var container = document.getElementById("spotii-popup__container");
  container.style.display = "none";
  document.removeEventListener("keydown", onDocumentKeyDown, false);
  destroy();
}

function onDocumentKeyDown(event) {
  if (event.keyCode === 27) {
    hide();
  }
}

function destroy() {
  backdrop.removeEventListener("click", hide, false);
  closeBtn.removeEventListener("click", hide, false);
  document.removeEventListener("keydown", onDocumentKeyDown, false);

  // Dettach from dom
  container.parentElement.removeChild(container);
  styles.parentElement.removeChild(styles);

  container = null;
  backdrop = null;
  wrap = null;
  content = null;
  closeBtn = null;
  styles = null;
}
