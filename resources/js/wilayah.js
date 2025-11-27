/* Reusable wilayah selector initializer for Profile + Registration forms.
 * Expects selects with IDs: province, regency or city, district, subdistrict
 * Hidden names (optional): province_name, regency_name, district_name, subdistrict_name
 * Hidden ids (optional): province_id, regency_id, district_id, subdistrict_id (used when selects are disabled)
 * Each select may have data-selected attribute to preselect stored value.
 */
(function () {
  const apiBase = '/api/wilayah';

  function q(id) { return document.getElementById(id); }

  function setHidden(sel, hidId, hidName) {
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;
    if (hidId) hidId.value = opt.value || '';
    if (hidName) hidName.value = opt.dataset?.label || opt.textContent || '';
  }

  function makeOption(val, label) {
    const o = document.createElement('option');
    o.value = val ?? '';
    o.textContent = label ?? '';
    o.dataset.label = label ?? '';
    return o;
  }

  async function get(url) {
    try {
      const r = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      let payload = null;
      try { payload = await r.json(); } catch (_) { /* ignore JSON parse errors */ }

      if (!r.ok) {
        const msg = (payload && (payload.message || payload.error)) || `HTTP ${r.status}`;
        console.warn(`Wilayah fetch failed: ${url} -> ${msg}`);
        return [];
      }

      if (Array.isArray(payload)) return payload;
      if (payload && Array.isArray(payload.data)) return payload.data;
      return [];
    } catch (e) {
      console.error('Wilayah fetch error:', e);
      return [];
    }
  }

  async function initWilayah() {
    const selProvince = q('province');
    const selRegency = q('regency') || q('city');
    const selDistrict = q('district');
    const selSubdistrict = q('subdistrict');

    if (!selProvince || !selRegency || !selDistrict || !selSubdistrict) return; // not present on this page

    const hidProvinceId = q('province_id');
    const hidProvinceName = q('province_name');
    const hidRegencyId = q('regency_id') || q('city_id');
    const hidRegencyName = q('regency_name') || q('city_name');
    const hidDistrictId = q('district_id');
    const hidDistrictName = q('district_name');
    const hidSubId = q('subdistrict_id');
    const hidSubName = q('subdistrict_name');

    const spProv = q('province_spinner');
    const spReg = q('regency_spinner');
    const spDis = q('district_spinner');
    const spSub = q('subdistrict_spinner');

    async function loadProvinces() {
      try {
        if (spProv) spProv.classList.remove('hidden');
        const data = await get(`${apiBase}/provinces`);
        selProvince.innerHTML = '';
        selProvince.appendChild(makeOption('', '-- Pilih Provinsi --'));
        data.forEach((p) => selProvince.appendChild(makeOption(p.id || p.province_id || p.value, p.name || p.province)));
        selProvince.disabled = false;
        const pre = selProvince.dataset.selected || hidProvinceId?.value;
        if (pre) {
          selProvince.value = pre;
          setHidden(selProvince, hidProvinceId, hidProvinceName);
          await onProvinceChange(false);
        }
      } finally {
        if (spProv) spProv.classList.add('hidden');
      }
    }

    async function onProvinceChange(clearDown = true) {
      const pid = selProvince.value;
      setHidden(selProvince, hidProvinceId, hidProvinceName);
      selRegency.innerHTML = '';
      selRegency.appendChild(makeOption('', '-- Pilih Kota/Kabupaten --'));
      selDistrict.innerHTML = '';
      selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
      selSubdistrict.innerHTML = '';
      selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
      selRegency.disabled = selDistrict.disabled = selSubdistrict.disabled = true;
      if (!pid) return;
      try {
        if (spReg) spReg.classList.remove('hidden');
        const data = await get(`${apiBase}/cities/${encodeURIComponent(pid)}`);
        data.forEach((c) => selRegency.appendChild(makeOption(c.id || c.city_id, (c.type ? c.type + ' ' : '') + (c.city_name || c.name || ''))));
        selRegency.disabled = false;
        const pre = selRegency.dataset.selected || hidRegencyId?.value;
        if (pre) {
          selRegency.value = pre;
          setHidden(selRegency, hidRegencyId, hidRegencyName);
          await onCityChange(false);
        }
      } finally {
        if (spReg) spReg.classList.add('hidden');
      }
    }

    async function onCityChange(clearDown = true) {
      const cid = selRegency.value;
      setHidden(selRegency, hidRegencyId, hidRegencyName);
      selDistrict.innerHTML = '';
      selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
      selSubdistrict.innerHTML = '';
      selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
      selDistrict.disabled = selSubdistrict.disabled = true;
      if (!cid) return;
      try {
        if (spDis) spDis.classList.remove('hidden');
        const data = await get(`${apiBase}/districts/${encodeURIComponent(cid)}`);
        data.forEach((d) => selDistrict.appendChild(makeOption(d.id || d.district_id, d.district_name || d.name)));
        selDistrict.disabled = false;
        const pre = selDistrict.dataset.selected || hidDistrictId?.value;
        if (pre) {
          selDistrict.value = pre;
          setHidden(selDistrict, hidDistrictId, hidDistrictName);
          await onDistrictChange(false);
        }
      } finally {
        if (spDis) spDis.classList.add('hidden');
      }
    }

    async function onDistrictChange(clearDown = true) {
      const did = selDistrict.value;
      setHidden(selDistrict, hidDistrictId, hidDistrictName);
      selSubdistrict.innerHTML = '';
      selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
      selSubdistrict.disabled = true;
      if (!did) return;
      try {
        if (spSub) spSub.classList.remove('hidden');
        const data = await get(`${apiBase}/sub-district/${encodeURIComponent(did)}`);
        data.forEach((s) => selSubdistrict.appendChild(makeOption(s.id || s.subdistrict_id, s.subdistrict_name || s.name)));
        selSubdistrict.disabled = false;
        const pre = selSubdistrict.dataset.selected || hidSubId?.value;
        if (pre) {
          selSubdistrict.value = pre;
          setHidden(selSubdistrict, hidSubId, hidSubName);
        }
      } finally {
        if (spSub) spSub.classList.add('hidden');
      }
    }

    selProvince.addEventListener('change', () => onProvinceChange());
    selRegency.addEventListener('change', () => onCityChange());
    selDistrict.addEventListener('change', () => onDistrictChange());
    selSubdistrict.addEventListener('change', () => setHidden(selSubdistrict, hidSubId, hidSubName));

    loadProvinces();
  }

  document.addEventListener('DOMContentLoaded', initWilayah);
})();
