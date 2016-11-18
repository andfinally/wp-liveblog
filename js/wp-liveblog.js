'use strict';

var domReady = function domReady(callback) {
  document.readyState === 'interactive' || document.readyState === 'complete' ? callback() : document.addEventListener('DOMContentLoaded', callback);
};

domReady(function () {
  var forms = document.querySelectorAll('.wp-liveblog-form');
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = forms[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var form = _step.value;

      form.addEventListener('submit', formHandler);
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }
});

var formHandler = function formHandler(e) {
  e.preventDefault();
  var data = {};
  data.title = e.target.querySelector('[name=wp-liveblog-title]').value || '';
  data.excerpt = e.target.querySelector('[name=wp-liveblog-excerpt]').value || '';
  data.content = e.target.querySelector('[name=wp-liveblog-content]').value || '';
  data.status = 'publish';
  data.type = 'wp_liveblog_post';
  data.post_tag = '1';
  var request = new XMLHttpRequest();
  request.onload = function (event) {
    console.log(event.target.response);
  };
  request.open('POST', POST_SUBMITTER.root + 'wp/v2/wp_liveblog_posts');
  request.setRequestHeader('Content-Type', 'application/json');
  request.setRequestHeader('X-WP-Nonce', POST_SUBMITTER.nonce);
  request.responseType = 'json';
  request.send(JSON.stringify(data));
};