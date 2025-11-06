document.addEventListener('DOMContentLoaded', () => {
  const sidepanel = document.getElementById('sidepanel');
  const openBtn   = document.getElementById('toggle-panel');
  const closeBtn  = document.getElementById('close-panel');

  if (!sidepanel || !openBtn || !closeBtn) return;

  const open = () => {
    sidepanel.classList.add('open');
    sidepanel.setAttribute('aria-hidden', 'false');
    document.body.classList.add('sidepanel-open');
  };

  const close = () => {
    sidepanel.classList.remove('open');
    sidepanel.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('sidepanel-open');
  };

  openBtn.addEventListener('click', open);
  closeBtn.addEventListener('click', close);

  // Close when clicking outside the panel (on the darkened backdrop)
  document.addEventListener('click', (e) => {
    const clickInside = sidepanel.contains(e.target) || openBtn.contains(e.target);
    if (!clickInside && sidepanel.classList.contains('open')) close();
  });

  // Close with ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidepanel.classList.contains('open')) close();
  });
});
