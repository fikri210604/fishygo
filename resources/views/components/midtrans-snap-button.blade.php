@props([
    'pesananId' => null,
    'label' => 'Bayar dengan Midtrans',
    'labelMidtrans' => 'Bayar Sekarang',
    'labelDefault' => null,
    'redirect' => null,
    'buttonId' => null,
    // Optional for checkout flow (create order then pay)
    'formSelector' => null,
    'createUrl' => null,
])

@php(
    $btnId = $buttonId ?: 'btn-pay-midtrans'
)

@php($labelDefault = $labelDefault ?: $label)

<button id="{{ $btnId }}" {{ $attributes->merge(['class' => 'btn btn-primary']) }}>
    {{ $labelDefault }}
    </button>

<script>
    (function(){
        const btnId = @json($btnId);
        let pesananId = @json($pesananId);
        let redirectAfter = @json($redirect ?: url()->current());
        const snapUrl = @json(route('payment.midtrans.snap'));
        const clientKey = @json(config('midtrans.client_key'));
        const snapSrc = @json(config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js');
        const formSelector = @json($formSelector);
        const createUrl = @json($createUrl);
        const labelMidtrans = @json($labelMidtrans);
        const labelDefault = @json($labelDefault);

        const btn = document.getElementById(btnId);
        if(!btn) return;
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

        function setBusy(v){
            btn.disabled = v;
            if(v) btn.classList.add('btn-disabled');
            else btn.classList.remove('btn-disabled');
        }

        function ensureSnapLoaded(){
            return new Promise((resolve, reject) => {
                if (window.snap) return resolve();
                // Avoid duplicate injection
                const existing = document.getElementById('midtrans-snap-js');
                if (existing) {
                    const check = () => { if (window.snap) resolve(); else setTimeout(check, 100); };
                    return check();
                }
                const s = document.createElement('script');
                s.src = snapSrc;
                s.id = 'midtrans-snap-js';
                if (clientKey) s.setAttribute('data-client-key', clientKey);
                s.onload = () => resolve();
                s.onerror = () => reject(new Error('Gagal memuat Midtrans Snap'));
                document.head.appendChild(s);
            });
        }

        function getForm(){ return formSelector ? document.querySelector(formSelector) : null; }

        function mountCheckoutHelpers(){
            const form = getForm();
            if (!form) return;
            const metodeInput = form.querySelector('#metode_pembayaran');
            const pickupInput = form.querySelector('#pickup');
            const payMethods = form.querySelector('#pay-methods');
            const codOptions = form.querySelector('#cod-options');
            const manualOptions = form.querySelector('#manual-options');
            const manualSelect = form.querySelector('#manual-bank-select');
            const manualHidden = form.querySelector('#manual_bank');
            const pickupAddress = form.querySelector('#pickup-address');
            const manualInstr = form.querySelector('#manual-instruction');
            const bankInfo = {
                BCA: 'Rekening BCA: 1234567890 a.n. PT FishyGo. Tambahkan berita: Kode Pesanan saat transfer.',
                BRI: 'Rekening BRI: 9876543210 a.n. PT FishyGo. Pastikan unggah bukti setelah transfer.',
                BNI: 'Rekening BNI: 1122334455 a.n. PT FishyGo. Simpan resi untuk validasi.',
            };

            function updateUI(){
                const selected = (metodeInput && metodeInput.value) ? metodeInput.value : 'midtrans';
                if (codOptions) codOptions.classList.toggle('hidden', selected !== 'cod');
                if (manualOptions) manualOptions.classList.toggle('hidden', selected !== 'manual');
                if (pickupAddress) pickupAddress.classList.toggle('hidden', !(pickupInput && pickupInput.value === '1'));
                // update manual instruction text
                if (manualInstr && manualHidden) {
                    const v = manualHidden.value;
                    if (v && bankInfo[v]) { manualInstr.textContent = bankInfo[v]; manualInstr.classList.remove('hidden'); }
                    else { manualInstr.textContent = ''; manualInstr.classList.add('hidden'); }
                }
                // Update button label based on method
                if (btn) {
                    btn.textContent = (selected === 'midtrans') ? labelMidtrans : labelDefault;
                }
            }

            if (payMethods) {
                payMethods.addEventListener('change', function(e){
                    if (e.target && e.target.matches('input.checkbox[data-value]')) {
                        const value = e.target.getAttribute('data-value');
                        Array.from(payMethods.querySelectorAll('input.checkbox[data-value]')).forEach(el => {
                            el.checked = (el.getAttribute('data-value') === value);
                        });
                        if (metodeInput) metodeInput.value = value;
                        updateUI();
                    }
                    if (e.target && e.target.matches('input.radio[name="__cod_type"]')) {
                        const p = e.target.getAttribute('data-pickup') || '0';
                        if (pickupInput) pickupInput.value = p;
                        updateUI();
                    }
                });
            }
            if (manualSelect && manualHidden) {
                manualSelect.addEventListener('change', function(){
                    manualHidden.value = this.value || '';
                    updateUI();
                });
            }
            updateUI();
        }

        mountCheckoutHelpers();

        async function createOrderIfNeeded(){
            const form = getForm();
            if (!form) return { ok: true };
            // Determine selected method
            const metodeInput = form.querySelector('#metode_pembayaran');
            const metode = metodeInput ? (metodeInput.value || 'midtrans') : 'midtrans';
            if (metode !== 'midtrans') {
                // Fallback to native form submit for non-midtrans methods
                form.submit();
                return { ok: false, handedToForm: true };
            }
            // Ensure we have endpoint to create order
            if (!createUrl) return { ok: false, error: 'Missing createUrl for checkout flow' };
            const payload = {
                alamat_id: form.alamat_id ? form.alamat_id.value : null,
                catatan: form.catatan ? form.catatan.value : null,
                metode_pembayaran: 'midtrans',
                pickup: !!(form.querySelector('#pickup') && form.querySelector('#pickup').value === '1'),
            };
            const res = await fetch(createUrl, {
                method: 'POST',
                headers: Object.assign({
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }, csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.pesanan_id) {
                return { ok: false };
            }
            pesananId = data.pesanan_id;
            if (data.redirect) redirectAfter = data.redirect;
            return { ok: true };
        }

        btn.addEventListener('click', async function(){
            try {
                setBusy(true);
                // If used in checkout, create order first (or fallback to form)
                const created = await createOrderIfNeeded();
                if (!created.ok) {
                    if (created.handedToForm) return; // form submission continues
                    setBusy(false);
                    return;
                }
                const res = await fetch(snapUrl, {
                    method: 'POST',
                    headers: Object.assign({
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }, csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                    credentials: 'same-origin',
                    body: JSON.stringify({ pesanan_id: pesananId })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.token) { setBusy(false); return; }
                await ensureSnapLoaded();
                if (!(window.snap && data.token)) { setBusy(false); return; }
                window.snap.pay(data.token, {
                    onSuccess: function(){ window.location.href = redirectAfter; },
                    onPending: function(){ window.location.href = redirectAfter; },
                    onError: function(){ window.location.href = redirectAfter; },
                    // Triggered when user clicks "Return to Merchant" or closes popup
                    onClose: function(){ window.location.href = redirectAfter; }
                });
            } catch (e) {
                setBusy(false);
            }
        });
    })();
</script>
