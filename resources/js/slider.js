function initSlider(wrapper) {
  const track = wrapper.querySelector('[data-slider-track]');
  if (!track) return;

  let isDown = false;
  let startX = 0;
  let scrollLeft = 0;

  const onMouseDown = (e) => {
    isDown = true;
    track.classList.add('slider-grabbing');
    startX = e.pageX - track.offsetLeft;
    scrollLeft = track.scrollLeft;
  };
  const onMouseLeave = () => {
    isDown = false;
    track.classList.remove('slider-grabbing');
  };
  const onMouseUp = () => {
    isDown = false;
    track.classList.remove('slider-grabbing');
  };
  const onMouseMove = (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.4;
    track.scrollLeft = scrollLeft - walk;
  };

  track.addEventListener('mousedown', onMouseDown);
  track.addEventListener('mouseleave', onMouseLeave);
  track.addEventListener('mouseup', onMouseUp);
  track.addEventListener('mousemove', onMouseMove);

  // Touch events
  track.addEventListener('touchstart', (e) => {
    startX = e.touches[0].pageX;
    scrollLeft = track.scrollLeft;
  }, { passive: true });
  track.addEventListener('touchmove', (e) => {
    const x = e.touches[0].pageX;
    const walk = (x - startX) * 1.4;
    track.scrollLeft = scrollLeft - walk;
  }, { passive: true });

  // Arrow buttons
  const leftBtn = wrapper.querySelector('[data-slider-left]');
  const rightBtn = wrapper.querySelector('[data-slider-right]');
  if (leftBtn) {
    leftBtn.addEventListener('click', () => {
      track.scrollBy({ left: -300, behavior: 'smooth' });
    });
  }
  if (rightBtn) {
    rightBtn.addEventListener('click', () => {
      track.scrollBy({ left: 300, behavior: 'smooth' });
    });
  }
}

function initAllSliders() {
  document.querySelectorAll('[data-slider]').forEach(initSlider);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllSliders);
} else {
  initAllSliders();
}
