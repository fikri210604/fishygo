(function () {
  const apiBase = '/api/wilayah';

  function q(id) {
    return document.getElementById(id);
  }

  function setHidden(sel, hidId, hidName) {
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    if (hidId) hidId.value = opt.value || '';
    if (hidName) hidName.value = (opt.dataset && opt.dataset.label) ? opt.dataset.label : (opt.textContent || '');
  }

  function makeOption(val, label) {
    const o = document.createElement('option');
    o.value = (val ?? '').toString();
    o.textContent = label ?? '';
    o.dataset.label = label ?? '';
    return o;
  }

  async function get(url) {
    try {
      const r = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });

      let json = null;
      try { json = await r.json(); } catch (_) { json = null; }

      if (!r.ok) return [];

      // Direct array
      if (Array.isArray(json)) return json;

      // Laravel response: { success: true, data: [...] }
      if (json && Array.isArray(json.data)) return json.data;

      // Nested: { success, data: { data: [...] } }
      if (json && json.data && Array.isArray(json.data.data)) return json.data.data;

      return [];
    } catch (err) {
      console.error('Fetch error:', err);
      return [];
    }
  }

  async function initWilayah() {
    const selProvince = q('province');
    const selRegency = q('regency') || q('city');
    const selDistrict = q('district');
    const selSubdistrict = q('subdistrict') || q('village');

    if (!selProvince) return;

    const hidProvinceId = q('province_id');
    const hidProvinceName = q('province_name');
    const hidRegencyId = q('regency_id');
    const hidRegencyName = q('regency_name');
    const hidDistrictId = q('district_id');
    const hidDistrictName = q('district_name');
    const hidSubId = q('subdistrict_id');
    const hidSubName = q('subdistrict_name');


    /* =======================
       LOAD PROVINSI
       ======================= */
    async function loadProvinces() {
      const data = await get(`${apiBase}/provinces`);

      selProvince.innerHTML = '';
      selProvince.appendChild(makeOption('', '-- Pilih Provinsi --'));

      data.forEach(p => {
        const id = p.id ?? p.province_id ?? p.code ?? p.value ?? '';
        const name = p.name ?? p.province ?? '';
        selProvince.appendChild(makeOption(id, name));
      });

      const pre = selProvince.dataset.selected || hidProvinceId?.value;
      if (pre) {
        selProvince.value = pre;
        setHidden(selProvince, hidProvinceId, hidProvinceName);
        onProvinceChange(false);
      }
    }


    /* =======================
       PROVINSI -> KABUPATEN/KOTA
       ======================= */
    async function onProvinceChange(clear = true) {
      const code = selProvince.value;
      setHidden(selProvince, hidProvinceId, hidProvinceName);
      if (selRegency) {
        selRegency.innerHTML = '';
        selRegency.appendChild(makeOption('', '-- Pilih Kabupaten/Kota --'));
        selRegency.disabled = true;
      }
      if (selDistrict) {
        selDistrict.innerHTML = '';
        selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
        selDistrict.disabled = true;
      }
      if (selSubdistrict) {
        selSubdistrict.innerHTML = '';
        selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan/Desa --'));
        selSubdistrict.disabled = true;
      }

      if (!code || !selRegency) return;

      const data = await get(`${apiBase}/regencies/${encodeURIComponent(code)}`);

      data.forEach(r => {
        const id = r.id ?? r.regency_id ?? r.city_id ?? r.code ?? r.value ?? '';
        const name = r.name ?? r.regency ?? r.city_name ?? '';
        selRegency.appendChild(makeOption(id, name));
      });

      selRegency.disabled = false;

      const pre = selRegency.dataset.selected || hidRegencyId?.value;
      if (pre) {
        selRegency.value = pre;
        setHidden(selRegency, hidRegencyId, hidRegencyName);
        onCityChange(false);
      }
    }


    /* =======================
       KABUPATEN -> KECAMATAN
       ======================= */
    async function onCityChange(clear = true) {
      const code = selRegency.value;
      setHidden(selRegency, hidRegencyId, hidRegencyName);
      if (selDistrict) {
        selDistrict.innerHTML = '';
        selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
        selDistrict.disabled = true;
      }
      if (selSubdistrict) {
        selSubdistrict.innerHTML = '';
        selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan/Desa --'));
        selSubdistrict.disabled = true;
      }

      if (!code || !selDistrict) return;

      const data = await get(`${apiBase}/districts/${encodeURIComponent(code)}`);

      data.forEach(d => {
        const id = d.id ?? d.district_id ?? d.code ?? d.value ?? '';
        const name = d.name ?? d.district ?? '';
        selDistrict.appendChild(makeOption(id, name));
      });

      selDistrict.disabled = false;

      const pre = selDistrict.dataset.selected || hidDistrictId?.value;
      if (pre) {
        selDistrict.value = pre;
        setHidden(selDistrict, hidDistrictId, hidDistrictName);
        onDistrictChange(false);
      }
    }


    /* =======================
       KECAMATAN -> DESA
       ======================= */
    async function onDistrictChange(clear = true) {
      const code = selDistrict.value;
      setHidden(selDistrict, hidDistrictId, hidDistrictName);
      if (selSubdistrict) {
        selSubdistrict.innerHTML = '';
        selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan/Desa --'));
        selSubdistrict.disabled = true;
      }

      if (!code || !selSubdistrict) return;

      const data = await get(`${apiBase}/villages/${encodeURIComponent(code)}`);

      data.forEach(v => {
        const id = v.id ?? v.village_id ?? v.subdistrict_id ?? v.code ?? v.value ?? '';
        const name = v.name ?? v.village ?? v.subdistrict_name ?? '';
        selSubdistrict.appendChild(makeOption(id, name));
      });

      selSubdistrict.disabled = false;

      const pre = selSubdistrict.dataset.selected || hidSubId?.value;
      if (pre) {
        selSubdistrict.value = pre;
        setHidden(selSubdistrict, hidSubId, hidSubName);
      }
    }

    /* EVENT */
    selProvince && selProvince.addEventListener('change', onProvinceChange);
    selRegency && selRegency.addEventListener('change', onCityChange);
    selDistrict && selDistrict.addEventListener('change', onDistrictChange);
    selSubdistrict && selSubdistrict.addEventListener('change', () =>
      setHidden(selSubdistrict, hidSubId, hidSubName)
    );

    loadProvinces();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWilayah);
  } else {
    initWilayah();
  }
})();
