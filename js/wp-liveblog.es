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
	let data = {};
	data.title = e.target.querySelector('[name=wp-liveblog-title]').value || '';
	data.excerpt = e.target.querySelector('[name=wp-liveblog-excerpt]').value || '';
	data.content = e.target.querySelector('[name=wp-liveblog-content]').value || '';
	data.status = 'publish';
	data.type = 'wp_liveblog_post';
	// data.terms = ['wp_liveblog_instance'] = ['xxx'];
	// data.terms['wp_liveblog_instance'] = 3;
	// data.terms['xxx'] = 'xxx';
	data.wp_liveblog_instance = 1;
	let request = new XMLHttpRequest();
	request.onload = (event) => {
		console.log(event.target.response)
	}
	request.open('POST', WP_LIVEBLOG_SUBMITTER.root + 'wp/v2/wp_liveblog_posts');
	request.setRequestHeader('Content-Type', 'application/json');
	request.setRequestHeader('X-WP-Nonce', WP_LIVEBLOG_SUBMITTER.nonce);
	request.responseType = 'json';
	request.send(JSON.stringify(data));
}
