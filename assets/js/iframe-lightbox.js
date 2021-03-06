/*!
 * modified Simple lightbox effect in pure JS
 * @see {@link https://github.com/squeral/lightbox}
 * @see {@link https://github.com/squeral/lightbox/blob/master/lightbox.js}
 * @params {Object} elem Node element
 * @params {Object} [rate] debounce rate, default 500ms
 * new IframeLightbox(elem)
 * passes jshint
 */
(function (root) {
	"use strict";
	var d = document,
	aEL = "addEventListener",
	gEBI = "getElementById",
	gEBCN = "getElementsByClassName",
	cE = "createElement",
	cL = "classList",
	aC = "appendChild",
	dS = "dataset",
	containerClass = "iframe-lightbox",
	isLoadedClass = "is-loaded",
	isOpenedClass = "is-opened",
	isShowingClass = "is-showing";
	var IframeLightbox = function (elem, rate) {
		this.trigger = elem;
		this.rate = rate || 500;
		this.el = d[gEBCN](containerClass)[0] || "";
		this.body = this.el ? this.el[gEBCN]("body")[0] : "";
		this.content = this.el ? this.el[gEBCN]("content")[0] : "";
		this.href = elem[dS].src || "";
		this.paddingBottom = elem[dS].paddingBottom || "";
		this.init();
	};
	IframeLightbox.prototype.init = function () {
		var _this = this;
		if (!this.el) {
			this.create();
		}
		var debounce = function (func, wait) {
			var timeout,
			args,
			context,
			timestamp;
			return function () {
				context = this;
				args = [].slice.call(arguments, 0);
				timestamp = new Date();
				var later = function () {
					var last = (new Date()) - timestamp;
					if (last < wait) {
						timeout = setTimeout(later, wait - last);
					} else {
						timeout = null;
						func.apply(context, args);
					}
				};
				if (!timeout) {
					timeout = setTimeout(later, wait);
				}
			};
		};
		var handleOpenIframeLightbox = function (e) {
			e.preventDefault();
			_this.open();
		};
		var debounceHandleOpenIframeLightbox = debounce(handleOpenIframeLightbox, this.rate);
		this.trigger[aEL]("click", debounceHandleOpenIframeLightbox);
	};
	IframeLightbox.prototype.create = function () {
		var _this = this,
		bd = d[cE]("div");
		bd.style.pointerEvents="none";
		this.el = d[cE]("div");
		this.content = d[cE]("div");
		this.body = d[cE]("div");
		this.el[cL].add(containerClass);
		bd[cL].add("backdrop");
		this.content[cL].add("content");
		this.body[cL].add("body");
		this.el[aC](bd);
		this.content[aC](this.body);
		this.content_holder = d[cE]("div");
		this.content_holder[cL].add("content-holder");
		this.content_holder[aC](this.content);
		this.el[aC](this.content_holder);
		d.body[aC](this.el);
		bd[aEL]("click", function () {
			_this.close();
		});
		var clearBody = function (e) {
			if (_this.isOpen()) {
				return;
			}
			_this.el[cL].remove(isShowingClass);
			_this.body.innerHTML = "";
		};
		this.el[aEL]("transitionend", clearBody, false);
		this.el[aEL]("webkitTransitionEnd", clearBody, false);
		this.el[aEL]("mozTransitionEnd", clearBody, false);
		this.el[aEL]("msTransitionEnd", clearBody, false);
	};
	IframeLightbox.prototype.loadIframe = function () {
		this.iframeId = containerClass + Date.now();
		this.body.innerHTML = '<iframe src="' + this.href + '" name="' + this.iframeId + '" id="' + this.iframeId + '" onload="this.style.opacity=1;" style="opacity:0;border:none;" scrolling="no" webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen="true" height="166" frameborder="no"></iframe>';
		(function (iframeId, body) {
			d[gEBI](iframeId).onload = function () {
				var that = this; 
				setTimeout(function(){
					that.style.opacity = 1;
				},6000)
				body[cL].add(isLoadedClass);
			};
		})(this.iframeId, this.body);
	};
	IframeLightbox.prototype.open = function () {
		this.loadIframe();
		if (this.paddingBottom) {
			this.content.style.paddingBottom = this.paddingBottom;
		} else {
			this.content.removeAttribute("style");
		}
		this.el[cL].add(isShowingClass);
		this.el[cL].add(isOpenedClass);
	};
	IframeLightbox.prototype.close = function () {
		this.el[cL].remove(isOpenedClass);
		this.body[cL].remove(isLoadedClass);
	};
	IframeLightbox.prototype.isOpen = function () {
		return this.el[cL].contains(isOpenedClass);
	};
	root.IframeLightbox = IframeLightbox;
})("undefined" !== typeof window ? window : this);

function removeOverlay(){
	document.body.querySelector(".sptii-overlay") ? document.body.querySelector(".sptii-overlay").remove() : ""
	document.body.querySelector(".blockUI.blockOverlay") ? document.body.querySelector(".blockUI.blockOverlay").remove() : ""
}

function onMessage(e) {
    var o = e.data,
		n = window.spotiiCheckoutUrl;
    "hideOverlay" === o ?  document.body.querySelector(".sptii-overlay") ? setTimeout(function(){ removeOverlay()}, 5000) : "" : "redirectToSpotii" === o && n ? setTimeout((function() {
        window.location = n
    }), 1e3) : "function" == typeof window[o.func] && window[o.func].call(null, o.message)
}

function parentFunc(e) {
    closeIFrame()
}
function closeIFrame() {
	document.body.querySelector("#closeiframebtn").click()
}
function keepIFrameOpen() {
    console.log("keep iframeopen called.."), document.body.querySelector("#closeclick").click()
}
function loadIFrame(){
	openIFrame()
	document.getElementsByClassName("fancy-box")[0].click()
}

window.addEventListener('keydown', function(e){
    if((e.key=='Escape'||e.key=='Esc'||e.keyCode==27)){
		closeIFrame();
    }
}, true);

window.addEventListener ? window.addEventListener("message", onMessage, !1) : window.attachEvent && window.attachEvent("onmessage", onMessage, !1);

function openIFrame() {
	
	(function(root, document) {
		"use strict";

		var btn = document.querySelector("#closeiframebtn");
		btn.addEventListener("click", function() {
			removeOverlay();
			document.body.querySelector(".backdrop").click();
		});

		var btn = document.querySelector(".fancy-box");
		btn.addEventListener("click", function(e) {
			e.preventDefault();
			removeOverlay();
			showOverlay();
			openIFrame();
		});

		[].forEach.call(document.getElementsByClassName("lightbox"), function(el) {
			el.lightbox = new IframeLightbox(el, {
				onCreated: function() {
					
				},
				onLoaded: function() {

				},
				onError: function() {
					/* hide your preloader */
				},
				onClosed: function() {
					/* hide your preloader */
				},
				scrolling: false,
				/* default: false */
				rate: 50 /* default: 500 */,
				touch: false /* default: false - use with care for responsive images in links on vertical mobile screens */
			});
		});
	 })("undefined" !== typeof window ? window : this, document);
	 
}
