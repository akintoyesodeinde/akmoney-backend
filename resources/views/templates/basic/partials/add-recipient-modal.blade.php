<!-- Add / Edit Recipient Modal -->
<div id="addRecipientModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-card-white rounded-3xl shadow-xl max-w-md w-full p-8 animate-in fade-in duration-300 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 id="modalTitle" class="text-xl font-bold text-text-primary">Add Recipient</h2>
            <button type="button" onclick="closeAddRecipientModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-bg-main hover:bg-border-gray transition-colors text-text-secondary">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <div id="recipient-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 text-center"></div>
        <div id="recipient-success" class="hidden mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 text-center"></div>

        <form id="recipientForm" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="recipientMethod" value="POST">
            <input type="hidden" id="recipientId" value="">
            <input type="hidden" id="recipientCountryId" value="">
            <input type="hidden" id="recipientDeliveryMethodId" value="">
            <input type="hidden" id="recipientServiceId" value="">
            <input type="hidden" id="recipientServiceFormData" value="">

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Full Name <span class="text-brand-gold">*</span></label>
                <input type="text" name="name" id="recipientName" required placeholder="e.g. John Doe"
                    class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Phone Number</label>
                <input type="tel" name="mobile" id="recipientPhone" placeholder="e.g. +1 555 000 1234"
                    class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Email Address</label>
                <input type="email" name="email" id="recipientEmail" placeholder="e.g. john@example.com"
                    class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Address</label>
                <input type="text" name="address" id="recipientAddress" placeholder="Street, City, Country"
                    class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAddRecipientModal()"
                    class="flex-1 px-4 py-3.5 bg-bg-main border border-border-gray text-text-secondary font-bold rounded-2xl hover:bg-border-gray transition-all text-sm">
                    Cancel
                </button>
                <button id="recipientSubmitBtn" type="button" onclick="submitRecipientForm()"
                    class="flex-1 px-4 py-3.5 bg-brand-gold text-white font-bold rounded-2xl hover:bg-brand-gold/90 transition-all text-sm flex items-center justify-center gap-2">
                    <i class="ri-save-line"></i>
                    <span id="recipientSubmitLabel">Save Recipient</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    function resetModal() {
        document.getElementById('recipientForm').reset();
        document.getElementById('recipientId').value              = '';
        document.getElementById('recipientMethod').value          = 'POST';
        document.getElementById('recipientCountryId').value       = '';
        document.getElementById('recipientDeliveryMethodId').value = '';
        document.getElementById('recipientServiceId').value       = '';
        document.getElementById('recipientServiceFormData').value = '';
        document.getElementById('recipient-error').classList.add('hidden');
        document.getElementById('recipient-success').classList.add('hidden');
        document.getElementById('recipientSubmitLabel').textContent = 'Save Recipient';
        document.getElementById('modalTitle').textContent = 'Add Recipient';
        document.getElementById('recipientSubmitBtn').disabled = false;
    }

    function setServiceContext(ctx) {
        ctx = ctx || {};
        document.getElementById('recipientCountryId').value        = ctx.country_id         || '';
        document.getElementById('recipientDeliveryMethodId').value = ctx.delivery_method_id || '';
        document.getElementById('recipientServiceId').value        = ctx.service_id         || '';
        document.getElementById('recipientServiceFormData').value  = ctx.service_form_data  || '';
    }

    window.openAddRecipientModal = function () {
        resetModal();
        // Read from the global context kept up-to-date by the send money form script.
        // On pages without that form, the global is undefined → setServiceContext gets an empty object.
        setServiceContext(window._smRecipientCtx || {});
        document.getElementById('addRecipientModal').classList.remove('hidden');
        document.getElementById('recipientName').focus();
    };

    window.openEditRecipientModal = function (id, name, mobile, email, address, serviceData) {
        resetModal();
        document.getElementById('recipientId').value      = id;
        document.getElementById('recipientMethod').value  = 'PUT';
        document.getElementById('recipientName').value    = name    || '';
        document.getElementById('recipientPhone').value   = mobile  || '';
        document.getElementById('recipientEmail').value   = email   || '';
        document.getElementById('recipientAddress').value = address || '';
        document.getElementById('modalTitle').textContent = 'Edit Recipient';
        document.getElementById('recipientSubmitLabel').textContent = 'Update Recipient';
        // If explicit service data is passed use it; otherwise inherit from the send money form.
        setServiceContext(serviceData || window._smRecipientCtx || {});
        document.getElementById('addRecipientModal').classList.remove('hidden');
        document.getElementById('recipientName').focus();
    };

    window.closeAddRecipientModal = function () {
        document.getElementById('addRecipientModal').classList.add('hidden');
    };

    window.submitRecipientForm = function () {
        const name             = document.getElementById('recipientName').value.trim();
        const mobile           = document.getElementById('recipientPhone').value.trim();
        const email            = document.getElementById('recipientEmail').value.trim();
        const address          = document.getElementById('recipientAddress').value.trim();
        const id               = document.getElementById('recipientId').value;
        const method           = document.getElementById('recipientMethod').value;
        const countryId        = document.getElementById('recipientCountryId').value;
        const deliveryMethodId = document.getElementById('recipientDeliveryMethodId').value;
        const serviceId        = document.getElementById('recipientServiceId').value;
        const serviceFormData  = document.getElementById('recipientServiceFormData').value;
        const errBox  = document.getElementById('recipient-error');
        const okBox   = document.getElementById('recipient-success');
        const btn     = document.getElementById('recipientSubmitBtn');

        errBox.classList.add('hidden');
        okBox.classList.add('hidden');

        if (!name) {
            errBox.textContent = 'Full name is required.';
            errBox.classList.remove('hidden');
            return;
        }

        const url = method === 'PUT'
            ? '/user/recipients/' + id
            : '{{ route("user.save_reps") }}';

        // For PUT we use fetch with JSON; for POST we submit via form (to handle redirect + flash)
        if (method === 'PUT') {
            btn.disabled = true;
            btn.querySelector('#recipientSubmitLabel').textContent = 'Saving…';

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name, mobile, email, address,
                    country_id:         countryId        || null,
                    delivery_method_id: deliveryMethodId || null,
                    service_id:         serviceId        || null,
                    service_form_data:  serviceFormData  || null,
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    okBox.textContent = 'Recipient updated successfully!';
                    okBox.classList.remove('hidden');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update.');
                    errBox.textContent = msg;
                    errBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.querySelector('#recipientSubmitLabel').textContent = 'Update Recipient';
                }
            })
            .catch(() => {
                errBox.textContent = 'Request failed. Please try again.';
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.querySelector('#recipientSubmitLabel').textContent = 'Update Recipient';
            });
        } else {
            // POST — build a real form submission so flash messages work
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const fields = {
                '_token':           document.querySelector('meta[name="csrf-token"]').content,
                name, mobile, email, address,
                country_id:         countryId,
                delivery_method_id: deliveryMethodId,
                service_id:         serviceId,
                service_form_data:  serviceFormData,
            };

            Object.entries(fields).forEach(([k, v]) => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = k;
                input.value = v;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    };

    window.deleteRecipient = function (id) {
        if (!confirm('Are you sure you want to delete this recipient?')) return;

        fetch('/user/recipients/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete recipient.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    };

    // Close on backdrop click
    document.getElementById('addRecipientModal').addEventListener('click', function (e) {
        if (e.target === this) closeAddRecipientModal();
    });
})();
</script>
