window.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', clickListener);
  document.addEventListener('touch', clickListener);
})

function clickListener(event) {
  let target = event.target,
      href   = target.getAttribute('href');

  if(href !== null) {
    event.preventDefault();

    let hash_split = href.split('#'),
        toggle_id  = target.getAttribute('data-toggle-id');

    if(toggle_id !== null) {
      let toggle_content = document.querySelectorAll('[data-toggle-content="'+toggle_id+'"]');

      toggle_content.forEach((tc, idx) => {
        if(tc.classList.contains('hide')) {
          tc.classList.remove('hide');
        }
        else {
          tc.classList.add('hide');
        }
      })

    }
    else if(hash_split[1].length > 0) {
      let hash_target = document.querySelector('id="'+hash_split[1]+'"'),
          coords      = (typeof hash_target === 'object') ? hash_target.getBoundingClientRect() : null;

      if(typeof hash_target === 'object') {
        window.scrollTo({ left: coords['x'], top: coords['y'], behavior: 'smooth' });
      }
    }
  }
}
