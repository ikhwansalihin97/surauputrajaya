{{--
    Pengesahan borang di sisi pelayar dengan mesej yang jelas.
    Gantikan tooltip pelayar (mudah terlepas pandang) dengan senarai medan yang perlu
    dibetulkan, serta tanda merah pada setiap medan. Borang mesti mempunyai atribut novalidate.

    Penggunaan: @include('surau._validasi-borang', ['formId' => 'idBorang'])
--}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById(@json($formId));
    if (!form) return;

    const labelFor = (el) => {
        const group = el.closest('[class*="col-"]') || el.parentElement;
        const label = group?.querySelector('label');
        let text = (label?.textContent || el.name || 'Medan').replace('*', '').trim();
        const row = el.closest('.ajk-row');
        if (row) {
            const bil = row.querySelector('.ajk-bil')?.textContent?.trim();
            if (bil) text = `${bil}: ${text}`;
        }
        return text;
    };

    const reasonFor = (el) => {
        const v = el.validity;
        if (v.valueMissing) return 'wajib diisi';
        if (v.patternMismatch) return 'hanya boleh mengandungi nombor';
        if (v.rangeUnderflow) return `mestilah sekurang-kurangnya ${el.min}`;
        if (v.rangeOverflow) return `tidak boleh melebihi ${el.max}`;
        if (v.stepMismatch) return 'nilai perpuluhan tidak sah';
        if (v.typeMismatch) return 'format tidak sah';
        if (v.tooLong) return `maksimum ${el.maxLength} aksara`;
        return el.validationMessage || 'tidak sah';
    };

    const escapeHtml = (s) => s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    // Buang tanda merah sebaik sahaja medan dibetulkan
    form.addEventListener('input', (e) => {
        if (e.target.classList.contains('is-invalid') && e.target.checkValidity()) {
            e.target.classList.remove('is-invalid');
        }
    });
    form.addEventListener('change', (e) => {
        if (e.target.classList.contains('is-invalid') && e.target.checkValidity()) {
            e.target.classList.remove('is-invalid');
        }
    });

    form.addEventListener('submit', function (e) {
        const invalid = [...form.querySelectorAll('input, select, textarea')]
            .filter(el => !el.disabled && el.offsetParent !== null && !el.checkValidity());

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        if (invalid.length === 0) return;

        e.preventDefault();
        invalid.forEach(el => el.classList.add('is-invalid'));

        const items = invalid.slice(0, 10)
            .map(el => `<li><strong>${escapeHtml(labelFor(el))}</strong> ${escapeHtml(reasonFor(el))}</li>`)
            .join('');
        const more = invalid.length > 10 ? `<li>dan ${invalid.length - 10} medan lain</li>` : '';

        const focusFirst = () => {
            invalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => invalid[0].focus({ preventScroll: true }), 300);
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Borang Belum Lengkap',
                html: `<div class="text-start small">Sila betulkan ${invalid.length} medan berikut:<ul class="mt-2 mb-0 ps-3">${items}${more}</ul></div>`,
                confirmButtonText: 'Betulkan Borang',
                confirmButtonColor: '#1e40af',
            }).then(focusFirst);
        } else {
            alert('Sila lengkapkan medan yang ditanda merah.');
            focusFirst();
        }
    });
});
</script>
