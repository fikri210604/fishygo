document.addEventListener("DOMContentLoaded", () => {

    const provinceEl   = document.getElementById("province");
    const cityEl       = document.getElementById("city");
    const districtEl   = document.getElementById("district");
    const subdistrictEl    = document.getElementById("subdistrict");

    const provinceId   = document.getElementById("province_id");
    const cityId       = document.getElementById("regency_id");
    const districtId   = document.getElementById("district_id");
    const subdistrictId    = document.getElementById("subdistrict_id");

    const provinceName = document.getElementById("province_name");
    const cityName     = document.getElementById("regency_name");
    const districtName = document.getElementById("district_name");
    const subdistrictName  = document.getElementById("subdistrict_name");

    const spinnerProv  = document.getElementById("province_spinner");
    const spinnerCity  = document.getElementById("regency_spinner");
    const spinnerDist  = document.getElementById("district_spinner");
    const spinnerVill  = document.getElementById("subdistrict_spinner");


    function startLoading(spinner, select) {
        spinner.classList.remove("hidden");
        select.disabled = true;
    }

    function stopLoading(spinner, select) {
        spinner.classList.add("hidden");
        select.disabled = false;
    }

    async function loadOptions(url, select, placeholder, spinner) {
        startLoading(spinner, select);
        select.innerHTML = `<option value="">${placeholder}</option>`;

        try {
            const res = await fetch(url);
            const json = await res.json();

            json.data.forEach(item => {
                select.innerHTML += `<option value="${item.id}" data-name="${item.name}">
                    ${item.name}
                </option>`;
            });

        } catch (e) {
            console.error("Load error:", e);
        }

        stopLoading(spinner, select);
    }


    loadOptions(
        "/api/wilayah/provinces",
        provinceEl,
        "-- Pilih Provinsi --",
        spinnerProv
    ).then(() => {
        if (provinceId.value) {
            provinceEl.value = provinceId.value;
            provinceName.value = provinceEl.selectedOptions[0]?.dataset.name || "";
            provinceEl.dispatchEvent(new Event("change"));
        }
    });


    provinceEl.addEventListener("change", () => {
        provinceId.value = provinceEl.value;
        provinceName.value = provinceEl.selectedOptions[0]?.dataset.name || "";

        cityEl.innerHTML = `<option value="">-- Pilih Kota/Kabupaten --</option>`;
        districtEl.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
        subdistrictEl.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;

        if (!provinceEl.value) return;

        loadOptions(
            `/api/wilayah/cities/${provinceEl.value}`,
            cityEl,
            "-- Pilih Kota/Kabupaten --",
            spinnerCity
        ).then(() => {
            if (cityId.value) {
                cityEl.value = cityId.value;
                cityName.value = cityEl.selectedOptions[0]?.dataset.name || "";
                cityEl.dispatchEvent(new Event("change"));
            }
        });
    });


    cityEl.addEventListener("change", () => {
        cityId.value = cityEl.value;
        cityName.value = cityEl.selectedOptions[0]?.dataset.name || "";

        districtEl.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
        subdistrictEl.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;

        if (!cityEl.value) return;

        loadOptions(
            `/api/wilayah/districts/${cityEl.value}`,
            districtEl,
            "-- Pilih Kecamatan --",
            spinnerDist
        ).then(() => {
            if (districtId.value) {
                districtEl.value = districtId.value;
                districtName.value = districtEl.selectedOptions[0]?.dataset.name || "";
                districtEl.dispatchEvent(new Event("change"));
            }
        });
    });

    districtEl.addEventListener("change", () => {
        districtId.value = districtEl.value;
        districtName.value = districtEl.selectedOptions[0]?.dataset.name || "";

        subdistrictEl.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;

        if (!districtEl.value) return;

        loadOptions(
            `/api/wilayah/subdistricts/${districtEl.value}`,
            subdistrictEl,
            "-- Pilih Kelurahan --",
            spinnerVill
        ).then(() => {
            if (subdistrictId.value) {
                subdistrictEl.value = subdistrictId.value;
                subdistrictName.value = subdistrictEl.selectedOptions[0]?.dataset.name || "";
            }
        });
    });


    subdistrictEl.addEventListener("change", () => {
        subdistrictId.value = subdistrictEl.value;
        subdistrictName.value = subdistrictEl.selectedOptions[0]?.dataset.name || "";
    });

});
