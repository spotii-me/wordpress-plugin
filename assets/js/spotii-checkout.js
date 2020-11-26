const root = document.getElementsByTagName('body')[0];


function isMobileSafari() {
    const ua = (window && window.navigator && window.navigator.userAgent) || '';
    const iOS = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
    const webkit = !!ua.match(/WebKit/i);
    return iOS && webkit && !ua.match(/CriOS/i);
}
const thirdPartySupported = root => {
    return new Promise((resolve, reject) => {
        const receiveMessage = function (evt) {
            if (evt.data === 'MM:3PCunsupported') {
                reject();
            } else if (evt.data === 'MM:3PCsupported') {
                resolve();
            }
        };
        window.addEventListener('message', receiveMessage, false);
        const frame = document.createElement('iframe');
        frame.src = 'https://mindmup.github.io/3rdpartycookiecheck/start.html';
        frame.style.display = 'none';
        root.appendChild(frame);
    });
};
var container, backdrop, styles, wrap, closeBtn, content, first = !0;

function setUrls() {
    var o = document.getElementById("spotii-popup__button"),
        e = document.getElementById("spotii-popup__terms");
    o.href = "https://spotii.me/how-it-works.html", e.href = "https://spotii.me/terms-and-conditions.html"
}! function (o) {
    "function" == typeof define && define.amd ? define(["jquery"], o) : "object" == typeof exports ? o(require("jquery")) : o(jQuery)
}(function (o) {
    function e(o) {
        return r.raw ? o : encodeURIComponent(o)
    }
    
    function t(o) {
        return r.raw ? o : decodeURIComponent(o)
    }
    
    function i(o) {
        return e(r.json ? JSON.stringify(o) : String(o))
    }
    
    function n(e, t) {
        var i = r.raw ? e : function (o) {
            0 === o.indexOf('"') && (o = o.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
            try {
                return o = decodeURIComponent(o.replace(p, " ")), r.json ? JSON.parse(o) : o
            } catch (o) {}
        }(e);
        return o.isFunction(t) ? t(i) : i
    }
    var p = /\+/g,
        r = o.cookie = function (p, s, a) {
            if (void 0 !== s && !o.isFunction(s)) {
                if ("number" == typeof (a = o.extend({}, r.defaults, a)).expires) {
                    var c = a.expires,
                        l = a.expires = new Date;
                    l.setTime(+l + 864e5 * c)
                }
                return document.cookie = [e(p), "=", i(s), a.expires ? "; expires=" + a.expires.toUTCString() : "", a.path ? "; path=" + a.path : "", a.domain ? "; domain=" + a.domain : "", a.secure ? "; secure" : ""].join("")
            }
            for (var d = p ? void 0 : {}, u = document.cookie ? document.cookie.split("; ") : [], m = 0, h = u.length; h > m; m++) {
                var g = u[m].split("="),
                    f = t(g.shift()),
                    _ = g.join("=");
                if (p && p === f) {
                    d = n(_, s);
                    break
                }
                p || void 0 === (_ = n(_)) || (d[f] = _)
            }
            return d
        };
    r.defaults = {}, o.removeCookie = function (e, t) {
        return void 0 !== o.cookie(e) && (o.cookie(e, "", o.extend({}, t, {
            expires: -1
        })), !o.cookie(e))
    }
});
var POPUP_CSS = ".spotii-popup{position:fixed;left:0;top:0;bottom:0;right:0;z-index:99998}.spotii-popup__backdrop{position:absolute;left:0;top:0;bottom:0;right:0;background:rgba(0,0,0,.5)}.spotii-popup__wrap{position:absolute;left:50%;top:50%;transform:translate3d(-50%,-50%,0);width:100%;max-width:754px,}.spotii-popup__content{color:#333;font-size:16px;text-align:center;box-sizing:border-box;background:#fff;border-radius:16px;padding:40px 32px 24px;box-shadow:0 4px 15px rgba(0,0,0,.15)}.spotii-popup__close{color:#666;position:absolute;right:24px;top:24px;cursor:pointer}.spotii-popup__close:hover{color:#333}.spotii-popup__logo{width:160px;margin:0 auto 16px}.spotii-popup__moto{font-weight:700;font-size:1.25em;margin:0 0 8px}.spotii-popup__moto b{color:#ff4d4a}.spotii-popup__statement{color:#666;margin:0 0 40px}.spotii-popup__row{overflow:hidden;text-align:center}.spotii-popup__col{display:inline-block;vertical-align:top;width:230px;box-sizing:border-box;padding-left:12px;padding-right:12px;margin-bottom:40px}.spotii-popup__icon{color:#ff4d4a;display:inline-block;vertical-align:middle;padding:20px;background:#066;background:rgba(255,77,74,.1);border-radius:50%;margin-bottom:16px}.spotii-popup__icon svg{vertical-align:middle}.spotii-popup__point-title{font-weight:700;font-size:1em;line-height:1.375;min-height:2.75em;margin:0 0 8px}.spotii-popup__point-text{color:#666;line-height:1.5;font-size:.875em;margin:0}.spotii-popup__cta{margin:0 0 40px}.spotii-popup .spotii-popup__button,.spotii-popup .spotii-popup__button:active,.spotii-popup .spotii-popup__button:hover,.spotii-popup .spotii-popup__button:visited{display:inline-block;vertical-align:middle;color:#fff;font-weight:700;font-size:.875em;text-align:center;text-decoration:none;padding:12px 40px;border-radius:8px;background:#fe6b5f;cursor:pointer;transition:color 180ms ease,background 180ms ease}.spotii-popup .spotii-popup__button:hover{background:#ff4d4a}.spotii-popup__footer{color:#999;font-size:.75em;margin:32px 0 0}.spotii-popup__footer a{color:#999}.spotii-animation-zoom-in{opacity:0;transform:scale(.9);transition:all .3s ease}.spotii-animation-zoom-in-enter{opacity:1;transform:scale(1)}@media (max-width:768px){.spotii-popup__wrap{position:relative;left:0;top:0;height:100%;transform:none;border-radius:0;overflow-y:auto}.spotii-popup__content{min-height:100%}}",
    POPUP_CONTENT = '<div class="spotii-popup__inner"> <div class="spotii-popup__logo"> <svg viewBox="0 0 575 156" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <path d="M93.4,42.68 L116.74,19.34 C102.292199,4.90544225 81.9818341,-2.03751498 61.7212349,0.532217389 C41.4606357,3.10194976 23.5267068,14.8955829 13.14,32.48 C39.0890855,17.1797853 72.1029078,21.3754119 93.4,42.68 Z" fill="#FFC4BE"></path> <path d="M23.33,112.75 L0,136.08 C14.4513957,150.506892 34.7594279,157.444629 55.0171766,154.875258 C75.2749252,152.305887 93.2078982,140.517891 103.6,122.94 C77.648524,138.237056 44.6360832,134.04624 23.33,112.75 Z" fill="#FF4B44"></path> <path d="M93.4,42.68 L23.33,112.75 C44.6360832,134.04624 77.648524,138.237056 103.6,122.94 C118.900215,96.9909145 114.704588,63.9770922 93.4,42.68 Z" fill="#FFC4BE"></path> <path d="M23.33,112.75 L93.4,42.68 C72.1029078,21.3754119 39.0890855,17.1797853 13.14,32.48 C-2.1570557,58.431476 2.03375993,91.4439168 23.33,112.75 Z" fill="#FF4B44"></path> <path d="M228,94.14 C228,108.71 216.85,122.94 194,122.94 C167.25,122.94 158.68,105.63 158,96.2 L180.12,92.2 C180.47,98.03 184.58,103.69 193.49,103.69 C200.18,103.69 203.44,100.09 203.44,96.32 C203.44,93.23 201.38,90.66 195.04,89.32 L185.27,87.09 C167.09,83.15 159.89,72.86 159.89,60.86 C159.89,45.26 173.61,32.57 192.64,32.57 C217.33,32.59 225.9,48 226.76,58 L205.15,61.95 C204.47,56.29 200.87,51.49 192.98,51.49 C187.98,51.49 183.72,54.4 183.72,58.86 C183.72,62.46 186.64,64.52 190.41,65.2 L201.72,67.43 C219.39,71 228,81.62 228,94.14 Z M425.84,77.72 C425.84,102.699839 405.589839,122.95 380.61,122.95 C355.630161,122.95 335.38,102.699839 335.38,77.72 C335.38,52.7401608 355.630161,32.49 380.61,32.49 C392.606555,32.4873468 404.112524,37.2517846 412.59537,45.7346302 C421.078215,54.2174759 425.842653,65.723445 425.84,77.72 Z M399.84,77.72 C399.84,65.99 391.24,56.49 380.64,56.49 C370.04,56.49 361.4,66 361.4,77.72 C361.4,89.44 370,98.94 380.61,98.94 C391.22,98.94 399.81,89.44 399.81,77.72 L399.84,77.72 Z M518.92,0 C511.740298,-4.39629938e-16 505.92,5.82029825 505.92,13 C505.92,20.1797017 511.740298,26 518.92,26 C526.099702,26 531.92,20.1797017 531.92,13 C531.92,9.55218563 530.550361,6.24558476 528.112388,3.80761184 C525.674415,1.36963892 522.367814,2.11117741e-16 518.92,0 Z M505.92,122.94 L532,122.94 L532,46.8 L505.89,32.48 L505.92,122.94 Z M561.94,49.7 C569.119702,49.7 574.94,43.8797017 574.94,36.7 C574.94,29.5202983 569.119702,23.7 561.94,23.7 C554.760298,23.7 548.94,29.5202983 548.94,36.7 C548.94,43.8797017 554.760298,49.7 561.94,49.7 Z M548.94,56.13 L548.94,122.94 L575,122.94 L575,70.45 L548.94,56.13 Z M447.18,32.48 L431.49,32.48 L431.49,58.64 L447.18,58.64 L447.18,94.21 C447.18,101.831403 450.208274,109.140506 455.598357,114.528714 C460.988441,119.916922 468.298598,122.942653 475.92,122.94 L488.92,122.940002 L488.92,96.88 L485.43,96.88 C478.69,96.88 473.23,90.88 473.23,83.4 L473.23,58.64 L488.92,58.64 L488.92,32.48 L473.24,32.48 L473.24,14.53 L447.1,0 L447.18,32.48 Z M265.33,115.93 L265.33,155.42 L239.26,141.1 L239.26,32.48 L265.33,32.48 L265.33,39.48 C271.920131,34.9262107 279.739582,32.4848123 287.75,32.48 C310.93,32.48 329.75,52.73 329.75,77.71 C329.75,102.69 310.96,122.94 287.75,122.94 C279.739292,122.927202 271.921215,120.482745 265.33,115.93 Z M265.33,78.45 C265.69,89.83 274.12,98.93 284.49,98.93 C295.1,98.93 303.7,89.43 303.7,77.71 C303.7,65.99 295.1,56.48 284.49,56.48 C274.12,56.48 265.69,65.59 265.33,76.96 L265.33,78.45 Z" fill="#FF4B44"></path> </svg> </div><p class="spotii-popup__moto"><b>Shop</b> now. <b>Live</b> now. <b>Pay</b> later.</p><p class="spotii-popup__statement">Spread your purchase over 4 cost-free instalments</p><div class="spotii-popup__row"><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></i> <p class="spotii-popup__point-title">Quick and easy sign-up</p><p class="spotii-popup__point-text">Simply select Spotii at check-out and fill in a few pieces of basic information.</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 19 22 12 13 5 13 19"></polygon><polygon points="2 19 11 12 2 5 2 19"></polygon></svg></i> <p class="spotii-popup__point-title">Instant approval and order completion</p><p class="spotii-popup__point-text">No extra wait! Your order ships right away without the immediate hit to your wallet.</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> </i> <p class="spotii-popup__point-title">No more fees!</p><p class="spotii-popup__point-text">No interest. No hidden fees. No surprises. Pay nothing extra when you pay on time.</p></div></div><div class="spotii-popup__cta"> <a class="spotii-popup__button" id="spotii-popup__button" href="#" target="_black" rel="nofollow noopener">Learn more</a> </div><div class="spotii-popup__footer">* Applicable for purchases over AED 200 or SAR 200. You must be over 18, a resident of the U.A.E or K.S.A and meet additional eligibility criteria to qualify. Late fees may apply. Estimated payment amounts shown on product pages exclude taxes and shipping charges, which are added at checkout. <a id="spotii-popup__terms" href="#" target="_black" rel="nofollow noopener">Click here</a> for complete terms and conditions. </div><div class="spotii-popup__close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div></div>',
    POPUP_CONTENT_ar = '<div class="spotii-popup__inner"> <div class="spotii-popup__logo"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 563.84 182"><defs><style>.cls-1{fill:#ff4b44;}.cls-2{fill:#ffc4be;}</style></defs><title>Spotti_Brand_Ar2</title><path class="cls-1" d="M1157.42,407.12V418.6c0,10.72-1.33,16.21-11,16.21h-19.76a57.94,57.94,0,0,1-13.34-1.19l-7.92-1.88v22.87l3.35.87c8.81,2.28,8.81,5.57,8.81,6.65a3,3,0,0,1-1,2.09c-1.65,1.63-7.58,5.4-27,5.18-13.55-.15-23.38-5.54-30.06-16.46l-1.33-2.18-24,12.31,1.5,2.52c11.52,19.4,29.46,29.64,51.89,29.64h.62c33.9-.28,55.8-11.17,55.8-27.74a20.69,20.69,0,0,0-1.92-9h5c27.49,0,36.77-14.27,36.77-44.69V392.57Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1417.91,406.19v20.08c0,6.82-.77,8.54-6.56,8.54s-6.44-2.66-6.44-5.15V401.82l-26.4,14.57v9.88c0,6.92-.6,8.54-6.56,8.54s-6.41-2.66-6.41-5.15V411.54l-26.38,14.53,0,1.58c-.15,5.59-1,7.11-6.31,7.16-5.18-.05-15.87-2.62-15.87-10.24V391.88l-26.41,14.55V422.6c0,2.65.12,4.87.21,6.66.11,2,.25,4.54-.08,5-.13.12-.86.52-4.44.52h-20.67V424.15c0-9.08-2.75-16.94-7.94-22.74-5.9-6.59-14.75-10.07-25.6-10.07-20.48,0-35.92,15.14-35.92,35.21,0,19.13,14.89,32,37.06,32h3.23c-2.16,1.68-6.46,3.25-15.23,3.25-2.76,0-5.84-.24-9.11-.49l-4-.31,1.46,24.3,2.38.2c3.51.29,7,.43,10.25.43,25.58,0,39.18-8.71,42.59-27.38h21.49c5,0,6.36-.33,9.08-1.15.61-.19,1.32-.4,2.23-.65a26.31,26.31,0,0,0,9.77-4.76c5.55,4.43,16.5,6.52,25.41,6.56h.29c10.58,0,16.62-1.35,21.76-4.95,6.49,4.36,14.4,4.95,20.73,4.95,8.5,0,14.78-1.49,20.07-4.82a36.69,36.69,0,0,0,18.75,4.82c17.94,0,30-10.92,30-27.18V391.64Zm-186.67,4a14.73,14.73,0,1,1-14.73,14.73A14.75,14.75,0,0,1,1231.24,410.17Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1109.44,500.05a11.48,11.48,0,1,0,11.48,11.48A11.48,11.48,0,0,0,1109.44,500.05Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1082.32,500.05a11.48,11.48,0,1,0,11.47,11.48A11.48,11.48,0,0,0,1082.32,500.05Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1151.79,367.25A12.25,12.25,0,1,0,1164,355,12.25,12.25,0,0,0,1151.79,367.25Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1146.27,389.48A12.25,12.25,0,1,0,1134,401.73,12.26,12.26,0,0,0,1146.27,389.48Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1280.53,470.84A11.48,11.48,0,1,0,1292,482.32,11.48,11.48,0,0,0,1280.53,470.84Z" transform="translate(-1034.19 -341)"/><path class="cls-2" d="M1574.69,383.68,1598,360.34a66.1,66.1,0,0,0-103.6,13.14A66.06,66.06,0,0,1,1574.69,383.68Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1504.62,453.75l-23.33,23.33a66.1,66.1,0,0,0,103.6-13.14A66.08,66.08,0,0,1,1504.62,453.75Z" transform="translate(-1034.19 -341)"/><path class="cls-2" d="M1574.69,383.68l-70.07,70.07a66.08,66.08,0,0,0,80.27,10.19A66.05,66.05,0,0,0,1574.69,383.68Z" transform="translate(-1034.19 -341)"/><path class="cls-1" d="M1504.62,453.75l70.07-70.07a66.06,66.06,0,0,0-80.27-10.2A66.09,66.09,0,0,0,1504.62,453.75Z" transform="translate(-1034.19 -341)"/></svg></div><p class="spotii-popup__moto">  <b> تسوق </b> الآن، <b> عش </b> الآن، <b> ادفع </b>لاحقا</p><p class="spotii-popup__statement">جزء الدفع على 4 أقساط متساوية خالية من التكاليف الاضافية</p><div class="spotii-popup__row"><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></i><p class="spotii-popup__point-title">!تسجيل دخول سهل وسريع</p><p class="spotii-popup__point-text">ببساطة اختر سبوتي عند الدفع واملئ بعض البيانات الاساسية</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 19 22 12 13 5 13 19"></polygon><polygon points="2 19 11 12 2 5 2 19"></polygon></svg></i>  <p class="spotii-popup__point-title">!موافقة فورية لتأكيد الطلب</p><p class="spotii-popup__point-text">لا داعي للانتظار! سيتم شحن طلبك فوراً بدون أي تأثير على محفظتك اليوم</p></div><div class="spotii-popup__col"> <i class="spotii-popup__icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> </i> <p class="spotii-popup__point-title">!لا يوجد سعر فائدة على الإطلاق</p><p class="spotii-popup__point-text">بدون تكاليف خفية، عند دفعك في الوقت المحدد لا يوجد أي تكاليف اضافية</p></div></div><div class="spotii-popup__cta"> <a class="spotii-popup__button" href="https://www.spotii.me/how-it-works.html?lang=ar" target="_black" rel="nofollow noopener">إقرأ المزيد</a> </div><div class="spotii-popup__footer"> * ينطبق على الدفعات بقيمة اكثر من 200 د.أ أو 200 ر.س. يجب أن يتجاوز عمرك الـ18 عاماً وينطبق عليك معايير قبول أخرى. قد تنطبق تكاليف للدفعات المتأخرة. المبلغ التقريبي المبين على صفحة المنتج لا تشمل الضرائب وتكاليف الشحن، ستتم اضافتهم في صفحة الدفع. <a href="https://www.spotii.me/terms-and-conditions.html?lang=ar">انقر هنا</a> للاطلاع على كل الأحكام والشروط</div><div class="spotii-popup__close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div></div>';
function render() {
    (container = document.createElement("div")).className = "spotii-popup", container.id = "spotii-popup__container", (backdrop = document.createElement("div")).className = "spotii-popup__backdrop", backdrop.addEventListener("click", hide, !1), container.appendChild(backdrop), (wrap = document.createElement("div")).className = "spotii-popup__wrap", container.appendChild(wrap), content = renderContent(), wrap.appendChild(content), (closeBtn = renderCloseButton()).addEventListener("click", hide, !1), content.appendChild(closeBtn), (styles = document.createElement("style")).innerHTML = POPUP_CSS, document.body.appendChild(styles), document.body.appendChild(container), setUrls(), show()
}

function renderContent() {
    var o = document.createElement("div");
    return o.id = "spotii-popup__content", o.className = "spotii-popup__content", o.innerHTML = (document.getElementsByTagName('html')[0].getAttribute('lang') == "ar") ? POPUP_CONTENT_ar: POPUP_CONTENT, o;
}

function renderCloseButton() {
    var o = document.createElement("div");
    return o.className = "spotii-popup__close", o.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>', o
}

function show() {
    var o = document.getElementById("spotii-popup__content");
    o.classList.add("spotii-animation-zoom-in"), document.getElementById("spotii-popup__container").style.display = "block", window.requestAnimationFrame(function () {
        o.classList.add("spotii-animation-zoom-in-enter")
    }), document.addEventListener("keydown", onDocumentKeyDown, !1)
}

function hide() {
    document.getElementById("spotii-popup__content").classList.remove("spotii-animation-zoom-in", "spotii-animation-zoom-in-enter"), document.getElementById("spotii-popup__container").style.display = "none", document.removeEventListener("keydown", onDocumentKeyDown, !1), destroy()
}

function onDocumentKeyDown(o) {
    27 === o.keyCode && hide()
}

function destroy() {
    backdrop.removeEventListener("click", hide, !1), closeBtn.removeEventListener("click", hide, !1), document.removeEventListener("keydown", onDocumentKeyDown, !1), container.parentElement.removeChild(container), styles.parentElement.removeChild(styles), container = null, backdrop = null, wrap = null, content = null, closeBtn = null, styles = null
}
jQuery(document).ready(function (o) {
    function e(o, e, t) {
        const i = document.createElement(o);
        return e && Object.keys(e).forEach(function (o) {
            i[o] = e[o]
        }), t && t.nodeType === Node.ELEMENT_NODE ? i.appendChild(t) : i.innerHTML = t, i
    }
    
    function checkBillingCountry(){
        var countrybilling = document.getElementById('billing_country').value;
       // console.log(country);
        if(countrybilling != "AE" && countrybilling != "SA"){
            document.getElementsByClassName('wc_payment_method payment_method_spotii_shop_now_pay_later')[0].style.display ="none";
        }else{
            document.getElementsByClassName('wc_payment_method payment_method_spotii_shop_now_pay_later')[0].style.display ="block";
        }
     }
     document.getElementById('billing_country').onchange = checkBillingCountry;

    function t() {
        const o = e("p", {}, function () {
                return navigator.vendor.startsWith('Apple')
            }() ? "Redirecting you to Spotii..." : "Checking your payment status with Spotii..."),
            t = e("span", {
                className: "sptii-text"
            }, o),
            i = e("span", {
                className: "sptii-loading"
            }, function () {
                const o = e("span");
                return o.className = "sptii-loading-icon", o.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024"><path d="M988 548c-19.9 0-36-16.1-36-36 0-59.4-11.6-117-34.6-171.3a440.45 440.45 0 0 0-94.3-139.9 437.71 437.71 0 0 0-139.9-94.3C629 83.6 571.4 72 512 72c-19.9 0-36-16.1-36-36s16.1-36 36-36c69.1 0 136.2 13.5 199.3 40.3C772.3 66 827 103 874 150c47 47 83.9 101.8 109.7 162.7 26.7 63.1 40.2 130.2 40.2 199.3.1 19.9-16 36-35.9 36z" fill="orange" /></svg>', o
            }()),
            n = e("span", {
                className: "sptii-spinnerText"
            }, t);
        return n.appendChild(i), n
    }
    showOverlay = function () {
        const o = e("div", {
                className: "sptii-overlay"
            }, ""),
            i = e("span", {
                className: "sptii-logo"
            }, function () {
                const o = e("span");
                return o.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 574.97 155.42"><defs><style>.cls-1{fill:#858585;}.cls-2{fill:#333;}</style></defs><title>Spotii_dark_logo</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Spotii_dark_logo"><path class="cls-1" d="M93.19,42.93l23.28-23.28A65.93,65.93,0,0,0,13.11,32.76,65.9,65.9,0,0,1,93.19,42.93Z"/><path class="cls-1" d="M93.19,42.93,23.28,112.84A65.93,65.93,0,0,0,103.37,123,65.93,65.93,0,0,0,93.19,42.93Z"/><path class="cls-2" d="M23.28,112.84,0,136.12A66,66,0,0,0,103.37,123,65.93,65.93,0,0,1,23.28,112.84Z"/><path class="cls-2" d="M23.28,112.84,93.19,42.93A65.9,65.9,0,0,0,13.11,32.76,65.9,65.9,0,0,0,23.28,112.84Z"/><path class="cls-2" d="M228,94.14c0,14.57-11.15,28.8-34,28.8-26.75,0-35.32-17.31-36-26.74l22.12-4c.35,5.83,4.46,11.49,13.37,11.49,6.69,0,9.95-3.6,9.95-7.37,0-3.09-2.06-5.66-8.4-7l-9.77-2.23c-18.18-3.94-25.38-14.23-25.38-26.23,0-15.6,13.72-28.29,32.75-28.29C217.33,32.59,225.9,48,226.76,58l-21.61,3.95c-.68-5.66-4.28-10.46-12.17-10.46-5,0-9.26,2.91-9.26,7.37,0,3.6,2.92,5.66,6.69,6.34l11.31,2.23C219.39,71,228,81.62,228,94.14ZM425.84,77.72a45.23,45.23,0,1,1-45.23-45.23A45.22,45.22,0,0,1,425.84,77.72Zm-26,0c0-11.73-8.6-21.23-19.2-21.23S361.4,66,361.4,77.72s8.6,21.22,19.21,21.22S399.81,89.44,399.81,77.72ZM518.92,0a13,13,0,1,0,13,13A13,13,0,0,0,518.92,0Zm-13,122.94H532V46.8L505.89,32.48ZM561.94,49.7a13,13,0,1,0-13-13A13,13,0,0,0,561.94,49.7Zm-13,6.43v66.81H575V70.45ZM447.18,32.48H431.49V58.64h15.69V94.21a28.73,28.73,0,0,0,28.74,28.73h13V96.88h-3.49c-6.74,0-12.2-6-12.2-13.48V58.64h15.69V32.48H473.24V14.53L447.1,0ZM265.33,115.93v39.49L239.26,141.1V32.48h26.07v7a39.48,39.48,0,0,1,22.42-7c23.18,0,42,20.25,42,45.23s-18.79,45.23-42,45.23A39.56,39.56,0,0,1,265.33,115.93Zm0-37.48c.36,11.38,8.79,20.48,19.16,20.48,10.61,0,19.21-9.5,19.21-21.22s-8.6-21.23-19.21-21.23c-10.37,0-18.8,9.11-19.16,20.48Z"/></g></g></g></svg>', o
            }());
        document.getElementsByTagName("body")[0].appendChild(o), o.appendChild(i), o.appendChild(t())
    }, openIframeSpotiiCheckout = function (e) {
        if (isMobileSafari()) {
            window.location.href = e
        } else {
            thirdPartySupported(root).then(() => {
                o(".fancy-box").attr("href", e).attr("data-src", e), loadIFrame()
            }).catch(() => {
                window.location.href = e
            });
        }
    }, spottiCapture = function (e, t, i = null, n = null, api = null) {
        var order_id = e;
        o.ajax({
            type: "post",
            dataType: "json",
            url: spotii_ajax.ajax_url,
            data: {
                action: "spotii_order_update",
                order_id: order_id,
                status: t,
                curr: i,
                total: n,
                api: api,
            },
            success: function (e) {
                "success" == e.result ? (o.removeCookie("orderId"), closeIFrame(), location.replace(e.redirect), console.log("done")) : (o(document).on("click", "#closeiframebtn", function () {
                    closeIFrame(), window.location.href = e.redirect
                }), console.log("error"))
            },
            error: function (e) {
                o(document).on("click", "#closeiframebtn", function () {
                    spottiCapture(order_id, "canceled");
                }), console.log("error " + e)
            }
        })
    };
    window.closeIFrameOnCompleteOrder = function ({
        status: e
    }) {
        if (console.log("Order state - ", e), first) {
            var t = o.cookie("orderId"),
                i = o.cookie("curr"),
                n = o.cookie("total");
                api = o.cookie("api");
            switch (o.removeCookie("total"), o.removeCookie("curr"), o.removeCookie("api"), o(".sptii-overlay").remove(), o(".sptii-content").remove(), e) {
            case "SUCCESS":
                spottiCapture(t, "completed", i, n, api);
                break;
            case "FAILED":
                var errorMessage = document.getElementsByTagName("html")[0].getAttribute("lang") == "ar" ? "لقد حصل خطأ عند الدفع عن طريق سبوتي، رجاءً حاول مرة اخرى" : "Payment with Spotii failed. Please try again";
                spottiCapture(t, "canceled", i, n, api), submit_error('<div class="woocommerce-error"></div>')
            }
        }
    }, submit_error = function (e) {
        var t = o("form.checkout");
        o(".woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message").remove(), t.prepend('<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + e + "</div>"), t.removeClass("processing"), o(document).find(".blockUI.blockOverlay").remove(), t.find(".input-text, select, input:checkbox").trigger("validate").blur(), removeOverlay(), o(document.body).trigger("checkout_error")
    }
});