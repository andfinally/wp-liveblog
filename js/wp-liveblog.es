let domReady = function(callback) {
  document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};

domReady(
  ()=> {
    console.log('wp-liveblog init');
  }
);
