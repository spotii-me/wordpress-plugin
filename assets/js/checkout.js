
        //Build fancybox component
        var button1 = document.createElement('button');
        button1.style.display='none';
        button1.id = 'closeclick';
        button1.textContent = 'set overlay closeClick to false';
        var bodyTag=document.getElementsByTagName('body')[0];
        bodyTag.appendChild(button1);

        var button2 = document.createElement('button');
        button2.style.display='none';
        button2.id = 'closeiframebtn';
        button2.textContent = 'set overlay closeClick to false';
        bodyTag.appendChild(button2);

        var div1 = document.createElement('div');
        div1.classList = 'fancy-box-container';
        bodyTag.appendChild(div1);

        var a1 = document.createElement('a');
        a1.id = 'fancy';
        a1.style.display='none';
        a1.classList= 'fancy-box';
        a1.textContent ='open fancybox';
        a1.href='';
        div1.appendChild(a1);
        //-----------------

        var failedCheckOutStatus = "FAILED";
        var submittedCheckOutStatus = "SUBMITTED";
        var successCheckOutStatus = "SUCCESS";
        const root=document.getElementsByTagName("body")[0];

        window.closeIFrameOnCompleteOrder = function(message) {
            var status = message.status;
            rejectUrl = message.rejectUrl;
            confirmUrl = message.confirmUrl;
            console.log("status -"+status);
            console.log("rejectUrl -"+rejectUrl);
            console.log("confirmUrl -"+confirmUrl);
            switch (status) {
              case successCheckOutStatus:
                console.log("successCheckOutStatus");
                document.getElementById("closeiframebtn").onclick = function() {
                    closeIFrame();
                    location.href = confirmUrl; 
                };
                removeOverlay();
                break;
              case failedCheckOutStatus:
                console.log("failedCheckOutStatus");
                document.getElementById("closeiframebtn").onclick = function() {
                    location.href = rejectUrl; 
                };
                removeOverlay();
                break;
              case submittedCheckOutStatus: 
                removeOverlay();
                break;
              default: 
                removeOverlay();
                break;
            }
            };
        //Check if browser support the popup
        const thirdPartySupported = root => {
        return new Promise((resolve, reject) => {
            const receiveMessage = function(evt) {
            if (evt.data === "MM:3PCunsupported") {
                reject();
            } else if (evt.data === "MM:3PCsupported") {
                resolve();
            }
            };
            window.addEventListener("message", receiveMessage, false);
            const frame = createElement("iframe", {
            src: "https://mindmup.github.io/3rdpartycookiecheck/start.html",
            });
            frame.style.display = "none";
            root.appendChild(frame);
        });
        };

        //Redirect to Spotii
        const redirectToSpotiiCheckout = function(checkoutUrl, timeout) {
        setTimeout(function() {
            window.location = checkoutUrl;
        }, timeout); 
        };

        //Check if its a safari broswer
        function isMobileSafari() {
        const ua = (window && window.navigator && window.navigator.userAgent) || "";
        const iOS = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
        const webkit = !!ua.match(/WebKit/i);
        return iOS && webkit && !ua.match(/CriOS/i);
        }

        //needed functions for the loadin page
        function createElement(tagName, attributes, content) {
        const el = document.createElement(tagName);
        if (attributes) {
            Object.keys(attributes).forEach(function(attr) {
                el[attr] = attributes[attr];
            });
        }
        if (content && content.nodeType === Node.ELEMENT_NODE) {
            el.appendChild(content);
        } else {
            el.innerHTML = content;
        }
        return el;
        }

        function Spinner() {
            const span = createElement("span");
            span.className = "sptii-loading-icon";
            span.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024"><path d="M988 548c-19.9 0-36-16.1-36-36 0-59.4-11.6-117-34.6-171.3a440.45 440.45 0 0 0-94.3-139.9 437.71 437.71 0 0 0-139.9-94.3C629 83.6 571.4 72 512 72c-19.9 0-36-16.1-36-36s16.1-36 36-36c69.1 0 136.2 13.5 199.3 40.3C772.3 66 827 103 874 150c47 47 83.9 101.8 109.7 162.7 26.7 63.1 40.2 130.2 40.2 199.3.1 19.9-16 36-35.9 36z" fill="orange" /></svg>';
            return span;
        }
        function Logo() {
            const span = createElement("span");
            span.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 574.97 155.42"><defs><style>.cls-1{fill:#858585;}.cls-2{fill:#333;}</style></defs><title>Spotii_dark_logo</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Spotii_dark_logo"><path class="cls-1" d="M93.19,42.93l23.28-23.28A65.93,65.93,0,0,0,13.11,32.76,65.9,65.9,0,0,1,93.19,42.93Z"/><path class="cls-1" d="M93.19,42.93,23.28,112.84A65.93,65.93,0,0,0,103.37,123,65.93,65.93,0,0,0,93.19,42.93Z"/><path class="cls-2" d="M23.28,112.84,0,136.12A66,66,0,0,0,103.37,123,65.93,65.93,0,0,1,23.28,112.84Z"/><path class="cls-2" d="M23.28,112.84,93.19,42.93A65.9,65.9,0,0,0,13.11,32.76,65.9,65.9,0,0,0,23.28,112.84Z"/><path class="cls-2" d="M228,94.14c0,14.57-11.15,28.8-34,28.8-26.75,0-35.32-17.31-36-26.74l22.12-4c.35,5.83,4.46,11.49,13.37,11.49,6.69,0,9.95-3.6,9.95-7.37,0-3.09-2.06-5.66-8.4-7l-9.77-2.23c-18.18-3.94-25.38-14.23-25.38-26.23,0-15.6,13.72-28.29,32.75-28.29C217.33,32.59,225.9,48,226.76,58l-21.61,3.95c-.68-5.66-4.28-10.46-12.17-10.46-5,0-9.26,2.91-9.26,7.37,0,3.6,2.92,5.66,6.69,6.34l11.31,2.23C219.39,71,228,81.62,228,94.14ZM425.84,77.72a45.23,45.23,0,1,1-45.23-45.23A45.22,45.22,0,0,1,425.84,77.72Zm-26,0c0-11.73-8.6-21.23-19.2-21.23S361.4,66,361.4,77.72s8.6,21.22,19.21,21.22S399.81,89.44,399.81,77.72ZM518.92,0a13,13,0,1,0,13,13A13,13,0,0,0,518.92,0Zm-13,122.94H532V46.8L505.89,32.48ZM561.94,49.7a13,13,0,1,0-13-13A13,13,0,0,0,561.94,49.7Zm-13,6.43v66.81H575V70.45ZM447.18,32.48H431.49V58.64h15.69V94.21a28.73,28.73,0,0,0,28.74,28.73h13V96.88h-3.49c-6.74,0-12.2-6-12.2-13.48V58.64h15.69V32.48H473.24V14.53L447.1,0ZM265.33,115.93v39.49L239.26,141.1V32.48h26.07v7a39.48,39.48,0,0,1,22.42-7c23.18,0,42,20.25,42,45.23s-18.79,45.23-42,45.23A39.56,39.56,0,0,1,265.33,115.93Zm0-37.48c.36,11.38,8.79,20.48,19.16,20.48,10.61,0,19.21-9.5,19.21-21.22s-8.6-21.23-19.21-21.23c-10.37,0-18.8,9.11-19.16,20.48Z"/></g></g></g></svg>';
            return span;
        }
        function SpinTextNode() {
            const text = isMobileSafari() ? "Redirecting you to Spotii..." : "Checking your payment status with Spotii...";
            const first= createElement("p", {}, text);
            const cont = createElement("span", {className: "sptii-text"}, first);
            const spinner = createElement("span", { className: "sptii-loading" }, Spinner());
            const spinText = createElement("span", { className: "sptii-spinnerText" }, cont);
            spinText.appendChild(spinner);
            return spinText;
        }
        //--------------------

        //Show the loading page
        function showOverlay() {
        console.log("showOverlay");
        const overlay = createElement("div", {className: "sptii-overlay"}, "");
        const logo = createElement("span", { className: "sptii-logo" }, Logo());
        document.getElementsByTagName("body")[0].appendChild(overlay);
        overlay.appendChild(logo);
        overlay.appendChild(SpinTextNode());
        }

        //Remove the loading page
        function removeOverlay() {
        console.log("removeOverlay");
        var overlay = document.getElementsByClassName("sptii-overlay")[0];
        document.getElementsByTagName("body")[0].removeChild(overlay);
        }
    define([
        "mage/storage",
        "jquery",     
        "mage/url",
        "mage/translate",
        ], function (
        storage,
        $,
        mageUrl,
        $t,
        ) { 
        var LoadCSS = function (filename) {
            var fileref = document.createElement("link");
            fileref.setAttribute("rel", "stylesheet");
            fileref.setAttribute("type", "text/css");
            fileref.setAttribute("href", filename);
    
            $("head").append(fileref);
        };    
        const openIframeSpotiiCheckout = function(url) {
            LoadCSS("https://widget.spotii.me/v1/javascript/fancybox-2.0.min.css");
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://widget.spotii.me/v1/javascript/fancybox-2.0.min.js';
            $("head").append(script);

            console.log("opened spotii iframe");
            // Make a post request to redirect
            $(".fancy-box").attr("href", url);
            openIFrame();
        };
        const continueToSpotiipay = function(url){
            console.log("continueToSpotiipay called");
            showOverlay();
            if (isMobileSafari()) {
            redirectToSpotiiCheckout(url,2500);
            } else  {
            thirdPartySupported(root).then( () => {
            openIframeSpotiiCheckout(url);
                }).catch(() => {
            redirectToSpotiiCheckout(url, 2500);
            });
            } 
        };


    });