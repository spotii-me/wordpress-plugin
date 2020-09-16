/*! jquery.cookie v1.4.1 | MIT */
var first = true;
!(function (a) {
  "function" == typeof define && define.amd
    ? define(["jquery"], a)
    : "object" == typeof exports
    ? a(require("jquery"))
    : a(jQuery);
})(function (a) {
  function b(a) {
    return h.raw ? a : encodeURIComponent(a);
  }
  function c(a) {
    return h.raw ? a : decodeURIComponent(a);
  }
  function d(a) {
    return b(h.json ? JSON.stringify(a) : String(a));
  }
  function e(a) {
    0 === a.indexOf('"') &&
      (a = a.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
    try {
      return (
        (a = decodeURIComponent(a.replace(g, " "))), h.json ? JSON.parse(a) : a
      );
    } catch (b) {}
  }
  function f(b, c) {
    var d = h.raw ? b : e(b);
    return a.isFunction(c) ? c(d) : d;
  }
  var g = /\+/g,
    h = (a.cookie = function (e, g, i) {
      if (void 0 !== g && !a.isFunction(g)) {
        if (((i = a.extend({}, h.defaults, i)), "number" == typeof i.expires)) {
          var j = i.expires,
            k = (i.expires = new Date());
          k.setTime(+k + 864e5 * j);
        }
        return (document.cookie = [
          b(e),
          "=",
          d(g),
          i.expires ? "; expires=" + i.expires.toUTCString() : "",
          i.path ? "; path=" + i.path : "",
          i.domain ? "; domain=" + i.domain : "",
          i.secure ? "; secure" : "",
        ].join(""));
      }
      for (
        var l = e ? void 0 : {},
          m = document.cookie ? document.cookie.split("; ") : [],
          n = 0,
          o = m.length;
        o > n;
        n++
      ) {
        var p = m[n].split("="),
          q = c(p.shift()),
          r = p.join("=");
        if (e && e === q) {
          l = f(r, g);
          break;
        }
        e || void 0 === (r = f(r)) || (l[q] = r);
      }
      return l;
    });
  (h.defaults = {}),
    (a.removeCookie = function (b, c) {
      return void 0 === a.cookie(b)
        ? !1
        : (a.cookie(b, "", a.extend({}, c, { expires: -1 })), !a.cookie(b));
    });
});
jQuery(document).ready(function ($) {
  function isMobileSafari() {
    const e = (window && window.navigator && window.navigator.userAgent) || "",
      t = !!e.match(/iPad/i) || !!e.match(/iPhone/i),
      a = !!e.match(/WebKit/i);
    return t && a && !e.match(/CriOS/i);
  }
  function createElement(e, t, a) {
    const n = document.createElement(e);
    return (
      t &&
        Object.keys(t).forEach(function (e) {
          n[e] = t[e];
        }),
      a && a.nodeType === Node.ELEMENT_NODE
        ? n.appendChild(a)
        : (n.innerHTML = a),
      n
    );
  }
  function Spinner() {
    const e = createElement("span");
    return (
      (e.className = "sptii-loading-icon"),
      (e.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024"><path d="M988 548c-19.9 0-36-16.1-36-36 0-59.4-11.6-117-34.6-171.3a440.45 440.45 0 0 0-94.3-139.9 437.71 437.71 0 0 0-139.9-94.3C629 83.6 571.4 72 512 72c-19.9 0-36-16.1-36-36s16.1-36 36-36c69.1 0 136.2 13.5 199.3 40.3C772.3 66 827 103 874 150c47 47 83.9 101.8 109.7 162.7 26.7 63.1 40.2 130.2 40.2 199.3.1 19.9-16 36-35.9 36z" fill="orange" /></svg>'),
      e
    );
  }
  function Logo() {
    const e = createElement("span");
    return (
      (e.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 574.97 155.42"><defs><style>.cls-1{fill:#858585;}.cls-2{fill:#333;}</style></defs><title>Spotii_dark_logo</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Spotii_dark_logo"><path class="cls-1" d="M93.19,42.93l23.28-23.28A65.93,65.93,0,0,0,13.11,32.76,65.9,65.9,0,0,1,93.19,42.93Z"/><path class="cls-1" d="M93.19,42.93,23.28,112.84A65.93,65.93,0,0,0,103.37,123,65.93,65.93,0,0,0,93.19,42.93Z"/><path class="cls-2" d="M23.28,112.84,0,136.12A66,66,0,0,0,103.37,123,65.93,65.93,0,0,1,23.28,112.84Z"/><path class="cls-2" d="M23.28,112.84,93.19,42.93A65.9,65.9,0,0,0,13.11,32.76,65.9,65.9,0,0,0,23.28,112.84Z"/><path class="cls-2" d="M228,94.14c0,14.57-11.15,28.8-34,28.8-26.75,0-35.32-17.31-36-26.74l22.12-4c.35,5.83,4.46,11.49,13.37,11.49,6.69,0,9.95-3.6,9.95-7.37,0-3.09-2.06-5.66-8.4-7l-9.77-2.23c-18.18-3.94-25.38-14.23-25.38-26.23,0-15.6,13.72-28.29,32.75-28.29C217.33,32.59,225.9,48,226.76,58l-21.61,3.95c-.68-5.66-4.28-10.46-12.17-10.46-5,0-9.26,2.91-9.26,7.37,0,3.6,2.92,5.66,6.69,6.34l11.31,2.23C219.39,71,228,81.62,228,94.14ZM425.84,77.72a45.23,45.23,0,1,1-45.23-45.23A45.22,45.22,0,0,1,425.84,77.72Zm-26,0c0-11.73-8.6-21.23-19.2-21.23S361.4,66,361.4,77.72s8.6,21.22,19.21,21.22S399.81,89.44,399.81,77.72ZM518.92,0a13,13,0,1,0,13,13A13,13,0,0,0,518.92,0Zm-13,122.94H532V46.8L505.89,32.48ZM561.94,49.7a13,13,0,1,0-13-13A13,13,0,0,0,561.94,49.7Zm-13,6.43v66.81H575V70.45ZM447.18,32.48H431.49V58.64h15.69V94.21a28.73,28.73,0,0,0,28.74,28.73h13V96.88h-3.49c-6.74,0-12.2-6-12.2-13.48V58.64h15.69V32.48H473.24V14.53L447.1,0ZM265.33,115.93v39.49L239.26,141.1V32.48h26.07v7a39.48,39.48,0,0,1,22.42-7c23.18,0,42,20.25,42,45.23s-18.79,45.23-42,45.23A39.56,39.56,0,0,1,265.33,115.93Zm0-37.48c.36,11.38,8.79,20.48,19.16,20.48,10.61,0,19.21-9.5,19.21-21.22s-8.6-21.23-19.21-21.23c-10.37,0-18.8,9.11-19.16,20.48Z"/></g></g></g></svg>'),
      e
    );
  }
  function SpinTextNode() {
    const e = createElement(
        "p",
        {},
        isMobileSafari()
          ? "Redirecting you to Spotii..."
          : "Checking your payment status with Spotii..."
      ),
      t = createElement("span", { className: "sptii-text" }, e),
      a = createElement("span", { className: "sptii-loading" }, Spinner()),
      n = createElement("span", { className: "sptii-spinnerText" }, t);
    return n.appendChild(a), n;
  }
  // Show Overlay 
  showOverlay = function () {
    const e = createElement("div", { className: "sptii-overlay" }, ""),
      t = createElement("span", { className: "sptii-logo" }, Logo());
    document.getElementsByTagName("body")[0].appendChild(e),
      e.appendChild(t),
      e.appendChild(SpinTextNode());
  };

  //Open lightbox
  openIframeSpotiiCheckout = function (checkoutUrl) {
    $(".fancy-box").attr("href", checkoutUrl).attr("data-src", checkoutUrl);
    loadIFrame();
  };
  // orderUpdate
  spottiCapture = function (orderId, status, curr, total) {
    $.ajax({
      type: "post",
      dataType: "json",
      url: spotii_ajax.ajax_url,
      data: {
        action: "spotii_order_update",
        order_id: orderId,
        status: status,
        curr: curr,
        total: total
      },
      success: function (data) {
      if(data.result == "success"){
          closeIFrame();
          location.replace(data.redirect);
          console.log("done");}
      else {
      $(document).on("click", "#closeiframebtn", function () {
        closeIFrame();
        window.location.href = data.redirect;
      });
      console.log("error");
		}
      },
      error: function (data) {
        $(document).on("click", "#closeiframebtn", function () {
          closeIFrame();
          window.location.href = data.redirect;
        });
        console.log("error "+data);
      },
    });
  };

  //Popup status
  var failedCheckOutStatus = "FAILED";
  var submittedCheckOutStatus = "SUBMITTED";
  var successCheckOutStatus = "SUCCESS";

  window.closeIFrameOnCompleteOrder = function ({ status }) {
    console.log("Order state - ", status);
    if (first){
      first=false;
      var orderId = $.cookie("orderId");
      var curr = $.cookie("curr");
      var total = $.cookie("total");
      $.removeCookie("total");
      $.removeCookie("curr");
      $.removeCookie("orderId");

      $(".sptii-overlay").remove();
      $(".sptii-content").remove();
      //const root = document.getElementById(config.parentElementId);
      switch (status) {
        case successCheckOutStatus: {
          spottiCapture(orderId, "completed", curr, total);
          break;
        }
        case failedCheckOutStatus: {
          spottiCapture(orderId, "canceled", curr, total);
          submit_error(
            '<div class="woocommerce-error">Payment with Spotii failed. Please try again</div>'
          ); // eslint-disable-line max-len
          break;
        }
    }
  }
};

  submit_error = function (error_message) {
    var checkout_form = $("form.checkout");
    $(
      ".woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message"
    ).remove();
    checkout_form.prepend(
      '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' +
        error_message +
        "</div>"
    ); // eslint-disable-line max-len
    checkout_form.removeClass("processing");
    $(document).find(".blockUI.blockOverlay").remove();
    checkout_form
      .find(".input-text, select, input:checkbox")
      .trigger("validate")
      .blur();
    $(document.body).trigger("checkout_error");
  };
});
