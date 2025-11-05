// Cascading wilayah selects — simple API-based fetcher
(function () {
  const API = {
    provinces: '/api/wilayah/provinces',
    regencies: (provId) => `/api/wilayah/regencies/${encodeURIComponent(provId)}`,
    districts: (regId) => `/api/wilayah/districts/${encodeURIComponent(regId)}`,
    villages: (distId) => `/api/wilayah/villages/${encodeURIComponent(distId)}`,
  };

  const cache = {
    provinces: null,
    regencies: new Map(),
    districts: new Map(),
    villages: new Map(),
  };

  const label = {
    provinces: 'Provinsi',
    regencies: 'Kabupaten/Kota',
    districts: 'Kecamatan',
    villages: 'Kelurahan/Desa',
  };

  const el = {};

  function opt(val, text) {
    const o = document.createElement('option');
    o.value = val || '';
    o.textContent = text || '';
    return o;
  }

  function setSelect(sel, items, kind) {
    sel.innerHTML = '';
    sel.appendChild(opt('', `-- Pilih ${label[kind]} --`));
    (items || []).forEach(i => sel.appendChild(opt(i.id, i.name)));
    sel.disabled = false;
  }

  async function fetchList(kind, key) {
    try {
      if (kind === 'provinces') {
        if (!cache.provinces) cache.provinces = await get(API.provinces);
        return cache.provinces;
      }
      const store = cache[kind];
      if (!store.has(key)) store.set(key, await get(API[kind](key)));
      return store.get(key);
    } catch (e) {
      console.error('Wilayah fetch failed', e);
      return [];
    }
  }

  async function get(url) {
    const res = await fetch(url);
    const json = await res.json();
    return Array.isArray(json) ? json : [];
  }

  function resetBelow(...sels) {
    for (const s of sels) {
      s.innerHTML = '';
      s.appendChild(opt('', '-- Pilih --'));
      s.disabled = true;
    }
  }

  async function init() {
    el.provSel = document.getElementById('province_select');
    if (!el.provSel) return; // page without wilayah fields
    el.regSel = document.getElementById('regency_select');
    el.distSel = document.getElementById('district_select');
    el.vilSel = document.getElementById('village_select');

    el.provId = document.getElementById('province_id');
    el.provName = document.getElementById('province_name');
    el.regId = document.getElementById('regency_id');
    el.regName = document.getElementById('regency_name');
    el.distId = document.getElementById('district_id');
    el.distName = document.getElementById('district_name');
    el.vilId = document.getElementById('village_id');
    el.vilName = document.getElementById('village_name');

    resetBelow(el.regSel, el.distSel, el.vilSel);

    setSelect(el.provSel, await fetchList('provinces'), 'provinces');

    // Restore existing selections if values exist
    if (el.provId?.value) el.provSel.value = el.provId.value;
    if (el.provSel.value) {
      setSelect(el.regSel, await fetchList('regencies', el.provSel.value), 'regencies');
      if (el.regId?.value) el.regSel.value = el.regId.value;
      if (el.regSel.value) {
        setSelect(el.distSel, await fetchList('districts', el.regSel.value), 'districts');
        if (el.distId?.value) el.distSel.value = el.distId.value;
        if (el.distSel.value) {
          setSelect(el.vilSel, await fetchList('villages', el.distSel.value), 'villages');
          if (el.vilId?.value) el.vilSel.value = el.vilId.value;
        }
      }
    }

    el.provSel.addEventListener('change', async () => {
      const val = el.provSel.value;
      const text = el.provSel.options[el.provSel.selectedIndex]?.text || '';
      if (el.provId) el.provId.value = val; if (el.provName) el.provName.value = text;
      // clear lower levels
      if (el.regId) el.regId.value = ''; if (el.regName) el.regName.value = '';
      if (el.distId) el.distId.value = ''; if (el.distName) el.distName.value = '';
      if (el.vilId) el.vilId.value = ''; if (el.vilName) el.vilName.value = '';
      resetBelow(el.regSel, el.distSel, el.vilSel);
      if (val) setSelect(el.regSel, await fetchList('regencies', val), 'regencies');
    });

    el.regSel.addEventListener('change', async () => {
      const val = el.regSel.value;
      const text = el.regSel.options[el.regSel.selectedIndex]?.text || '';
      if (el.regId) el.regId.value = val; if (el.regName) el.regName.value = text;
      if (el.distId) el.distId.value = ''; if (el.distName) el.distName.value = '';
      if (el.vilId) el.vilId.value = ''; if (el.vilName) el.vilName.value = '';
      resetBelow(el.distSel, el.vilSel);
      if (val) setSelect(el.distSel, await fetchList('districts', val), 'districts');
    });

    el.distSel.addEventListener('change', async () => {
      const val = el.distSel.value;
      const text = el.distSel.options[el.distSel.selectedIndex]?.text || '';
      if (el.distId) el.distId.value = val; if (el.distName) el.distName.value = text;
      if (el.vilId) el.vilId.value = ''; if (el.vilName) el.vilName.value = '';
      resetBelow(el.vilSel);
      if (val) setSelect(el.vilSel, await fetchList('villages', val), 'villages');
    });

    el.vilSel.addEventListener('change', () => {
      const val = el.vilSel.value;
      const text = el.vilSel.options[el.vilSel.selectedIndex]?.text || '';
      if (el.vilId) el.vilId.value = val; if (el.vilName) el.vilName.value = text;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

