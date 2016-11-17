let domReady = (callback) => {
  document.readyState === 'interactive' || document.readyState === 'complete' ? callback() : document.addEventListener('DOMContentLoaded', callback);
};

domReady(
  () => {
    let forms = document.querySelectorAll('.wp-liveblog-form');
    for (let form of forms) {
      form.addEventListener('submit', formHandler);
    }
  }
);

let formHandler = (e) => {
  e.preventDefault();
  let title = e.target.querySelector('[name=wp-liveblog-title]').value || '';
  let excerpt = e.target.querySelector('[name=wp-liveblog-excerpt]').value || '';
  let content = e.target.querySelector('[name=wp-liveblog-content]').value || '';
}
