(() => {
  const pre = document.getElementById('etch-content');
  const status = document.getElementById('status');
  const id = new URLSearchParams(window.location.search).get('id') || '10728';

  async function load() {
    status.textContent = `Loading entry ${id}...`;
    try {
      const res = await fetch(`/raw_response.php?id=${encodeURIComponent(id)}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const text = await res.text();
      pre.textContent = text;
      status.textContent = `Rendered id ${id} (raw, unmodified).`;
    } catch (err) {
      status.textContent = `Failed to load id ${id}: ${err.message}`;
    }
  }

  load();
})();
