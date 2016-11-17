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
  var title = e.target.querySelector('[name=wp-liveblog-title]').value || '';
  var excerpt = e.target.querySelector('[name=wp-liveblog-excerpt]').value || '';
  var content = e.target.querySelector('[name=wp-liveblog-content]').value || '';
};