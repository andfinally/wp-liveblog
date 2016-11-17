"use strict";

var domReady = function domReady(callback) {
  document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};

domReady(function () {
  console.log('wp-liveblog init');
});