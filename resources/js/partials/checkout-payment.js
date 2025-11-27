(function(){
    const container = document.getElementById('pay-methods');
    const hidden = document.getElementById('metode_pembayaran');
    const pickHidden = document.getElementById('pickup');
    const codOptions = document.getElementById('cod-options');
    const pickupAddress = document.getElementById('pickup-address');

    if (!container || !hidden) return;

    // === Update Payment Selection ===
    function update(el){
        const val = el.dataset.value;
        hidden.value = val;

        // Toggle checkbox
        container.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.checked = (cb === el);
        });

        // COD options
        if (val === 'cod') codOptions.classList.remove('hidden');
        else {
            codOptions.classList.add('hidden');
            pickHidden.value = '0';
            pickupAddress.classList.add('hidden');
        }
    }

    // Load initial state
    const current = hidden.value;
    container.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = cb.dataset.value === current;
        cb.addEventListener('change', () => update(cb));
    });

    // COD radios
    const radios = container.querySelectorAll('input[name="__cod_type"]');
    radios.forEach(r => r.addEventListener('change', function(){
        const isPickup = this.dataset.pickup === '1';
        pickHidden.value = isPickup ? '1' : '0';

        if (isPickup) pickupAddress.classList.remove('hidden');
        else pickupAddress.classList.add('hidden');
    }));

})();
