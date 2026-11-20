@extends($activeTemplate . 'layouts.master')
@section('activePage', 'recipients')
@section('content')

<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-brand-teal mb-2">Recipients</h1>
    <p class="text-text-secondary">Manage your saved recipients and send money quickly</p>
</div>

{{-- Search + Add --}}
<div class="flex flex-col md:flex-row gap-4 mb-8">
    <form method="GET" action="{{ route('user.reps') }}" class="flex-1 relative">
        <input type="text" name="search" placeholder="Search by name or email..."
            value="{{ request('search') }}"
            class="w-full px-4 py-3 pl-11 bg-white border border-border-gray rounded-2xl
                   focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none
                   transition-all text-sm">
        <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-text-secondary"></i>
    </form>
    <button onclick="openAddRecipientModal()"
        class="px-6 py-3 bg-brand-teal text-white font-bold rounded-2xl hover:bg-opacity-90
               transition-all flex items-center gap-2 justify-center">
        <i class="ri-add-line"></i> Add Recipient
    </button>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
    {{ $errors->first() }}
</div>
@endif

@if(session('success') || session('notify'))
<div class="mb-4 px-4 py-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-xl text-sm">
    {{ session('success') }}
</div>
@endif

@if(!$reps->count())
<div class="text-center py-12">
    <div class="w-20 h-20 bg-bg-main rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="ri-contacts-book-2-line text-3xl text-text-secondary"></i>
    </div>
    <h3 class="text-lg font-bold text-text-primary mb-2">No Recipients Found</h3>
    <p class="text-text-secondary mb-6">Start adding recipients to send money faster</p>
    <button onclick="openAddRecipientModal()"
        class="px-6 py-3 bg-brand-teal text-white font-bold rounded-2xl hover:bg-opacity-90 transition-all inline-flex items-center gap-2">
        <i class="ri-add-line"></i> Add First Recipient
    </button>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($reps as $rep)
    <div class="bg-card-white rounded-2xl p-6 border border-border-gray shadow-sm hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-brand-teal/10 rounded-full flex items-center justify-center">
                    <i class="ri-user-3-fill text-brand-teal text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-text-primary">{{ $rep->name }}</h3>
                    @if($rep->country)
                        <p class="text-xs text-text-secondary">{{ $rep->country->name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-1">
                <button onclick="openEditModal({{ $rep->id }})"
                    class="text-text-secondary hover:text-brand-teal transition-colors p-2 rounded-lg hover:bg-bg-main" title="Edit">
                    <i class="ri-edit-line"></i>
                </button>
                <button onclick="deleteRecipient({{ $rep->id }})"
                    class="text-text-secondary hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50" title="Delete">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>

        {{-- Contact info --}}
        <div class="space-y-2 mb-4 text-xs">
            @if($rep->mobile)
            <div class="flex justify-between">
                <span class="text-text-secondary">Phone</span>
                <span class="text-text-primary font-medium">{{ $rep->mobile }}</span>
            </div>
            @endif
            @if($rep->email)
            <div class="flex justify-between">
                <span class="text-text-secondary">Email</span>
                <span class="text-text-primary font-medium break-all">{{ $rep->email }}</span>
            </div>
            @endif
        </div>

        {{-- Method / Service / Account details --}}
        @if($rep->deliveryMethod || $rep->service || $rep->service_form_data)
        <div class="border-t border-border-gray pt-3 mt-3 space-y-2 text-xs">
            @if($rep->deliveryMethod)
            <div class="flex justify-between">
                <span class="text-text-secondary">Method</span>
                <span class="font-semibold text-brand-teal">{{ $rep->deliveryMethod->name }}</span>
            </div>
            @endif
            @if($rep->service)
            <div class="flex justify-between">
                <span class="text-text-secondary">Service</span>
                <span class="font-semibold text-text-primary">{{ $rep->service->name }}</span>
            </div>
            @endif
            @if($rep->service_form_data)
                @foreach($rep->service_form_data as $field)
                    @if(!empty($field['value']) && ($field['type'] ?? '') !== 'file')
                    <div class="flex justify-between gap-2">
                        <span class="text-text-secondary">{{ $field['name'] ?? $field['label'] ?? 'Field' }}</span>
                        <span class="font-medium text-text-primary text-right">
                            {{ is_array($field['value']) ? implode(', ', $field['value']) : $field['value'] }}
                        </span>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
        @endif

        <a href="{{ route('user.send.money.now') }}?recipient={{ $rep->id }}"
            class="mt-4 w-full bg-brand-teal text-white font-bold py-2.5 rounded-xl
                   hover:bg-opacity-90 transition-all flex items-center justify-center gap-2 text-sm">
            <i class="ri-send-plane-fill"></i> Send Money
        </a>
    </div>
    @endforeach
</div>
@endif

{{-- ══ ADD RECIPIENT MODAL ══ --}}
<div id="addRecipientModal"
    class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray flex-shrink-0">
            <h3 class="text-lg font-extrabold text-text-primary">Add Recipient</h3>
            <button onclick="closeModal('addRecipientModal')"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('user.save_reps') }}" id="addRecipientForm"
              class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4">

                {{-- Basic info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Full Name *</label>
                        <input type="text" name="name" required placeholder="Full name"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Phone</label>
                        <input type="tel" name="mobile" placeholder="+1 555 000 0000"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email</label>
                        <input type="email" name="email" placeholder="email@example.com"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Address</label>
                        <input type="text" name="address" placeholder="Address"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                </div>

                <hr class="border-border-gray">
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Delivery & Account Details</p>

                {{-- Country --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Recipient Country</label>
                    <select name="country_id" id="addCountrySelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select country —</option>
                        @foreach($receivingCountries as $country)
                        <option value="{{ $country->id }}"
                            data-delivery_methods="{{ json_encode($country->countryDeliveryMethods) }}">
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Delivery Method --}}
                <div id="addMethodWrap" class="hidden">
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Delivery Method</label>
                    <select name="delivery_method_id" id="addMethodSelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select method —</option>
                    </select>
                </div>

                {{-- Service --}}
                <div id="addServiceWrap" class="hidden">
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Service</label>
                    <select name="service_id" id="addServiceSelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select service —</option>
                    </select>
                </div>

                {{-- Service form fields --}}
                <div id="addServiceFormWrap" class="hidden space-y-3"></div>

                {{-- Hidden serialised service_form_data --}}
                <input type="hidden" name="service_form_data" id="addServiceFormData">

            </div>
            <div class="px-6 pb-6 flex-shrink-0">
                <button type="submit"
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm">
                    Save Recipient
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ EDIT RECIPIENT MODAL ══ --}}
<div id="editRecipientModal"
    class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray flex-shrink-0">
            <h3 class="text-lg font-extrabold text-text-primary">Edit Recipient</h3>
            <button onclick="closeModal('editRecipientModal')"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" id="editRecipientForm" class="flex flex-col flex-1 overflow-hidden">
            @csrf @method('PUT')
            <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Full Name *</label>
                        <input type="text" name="name" id="editName" required
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Phone</label>
                        <input type="tel" name="mobile" id="editMobile"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Email</label>
                        <input type="email" name="email" id="editEmail"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Address</label>
                        <input type="text" name="address" id="editAddress"
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                </div>

                <hr class="border-border-gray">
                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Delivery & Account Details</p>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Recipient Country</label>
                    <select name="country_id" id="editCountrySelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select country —</option>
                        @foreach($receivingCountries as $country)
                        <option value="{{ $country->id }}"
                            data-delivery_methods="{{ json_encode($country->countryDeliveryMethods) }}">
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="editMethodWrap" class="hidden">
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Delivery Method</label>
                    <select name="delivery_method_id" id="editMethodSelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select method —</option>
                    </select>
                </div>

                <div id="editServiceWrap" class="hidden">
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Service</label>
                    <select name="service_id" id="editServiceSelect"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                        <option value="">— Select service —</option>
                    </select>
                </div>

                <div id="editServiceFormWrap" class="hidden space-y-3"></div>
                <input type="hidden" name="service_form_data" id="editServiceFormData">

            </div>
            <div class="px-6 pb-6 flex-shrink-0">
                <button type="submit"
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm">
                    Update Recipient
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Recipient data for JS --}}
<script>
const RECIPIENTS_DATA = @json($reps->keyBy('id'));
const SERVICES_URL    = "{{ route('services') }}";
const SERVICE_FORM_URL = "{{ route('service.form') }}";
const FIELD_CLASSES   = 'w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent';

function openModal(id)  { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

document.querySelectorAll('[id$="Modal"]').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
});

function openAddRecipientModal() { openModal('addRecipientModal'); }

// ── Country → Delivery Methods ───────────────────────────────────────────────
function onCountryChange(countrySelect, methodSelect, methodWrap, serviceSelect, serviceWrap, formWrap) {
    const opt = countrySelect.options[countrySelect.selectedIndex];
    const raw = opt.dataset.delivery_methods;
    methodSelect.innerHTML = '<option value="">— Select method —</option>';
    serviceSelect.innerHTML = '<option value="">— Select service —</option>';
    serviceWrap.classList.add('hidden');
    formWrap.classList.add('hidden');
    formWrap.innerHTML = '';

    if (!raw) { methodWrap.classList.add('hidden'); return; }

    try {
        const methods = JSON.parse(raw);
        if (!methods.length) { methodWrap.classList.add('hidden'); return; }
        methods.forEach(m => {
            if (m.delivery_method) {
                const o = document.createElement('option');
                o.value = m.delivery_method.id;
                o.textContent = m.delivery_method.name;
                methodSelect.appendChild(o);
            }
        });
        methodWrap.classList.remove('hidden');
    } catch(e) { methodWrap.classList.add('hidden'); }
}

// ── Delivery Method → Services ───────────────────────────────────────────────
function onMethodChange(countrySelect, methodSelect, serviceSelect, serviceWrap, formWrap, preselectServiceId) {
    const countryId = countrySelect.value;
    const methodId  = methodSelect.value;
    serviceSelect.innerHTML = '<option value="">— Select service —</option>';
    serviceWrap.classList.add('hidden');
    formWrap.classList.add('hidden');
    formWrap.innerHTML = '';
    if (!countryId || !methodId) return;

    fetch(SERVICES_URL + '?country_id=' + countryId + '&delivery_method_id=' + methodId)
        .then(r => r.json())
        .then(data => {
            const services = data.data?.services ?? [];
            if (!services.length) { serviceWrap.classList.add('hidden'); return; }
            services.forEach(s => {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                serviceSelect.appendChild(o);
            });
            serviceWrap.classList.remove('hidden');
            if (preselectServiceId) {
                serviceSelect.value = preselectServiceId;
                serviceSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(() => {});
}

// ── Service → Form Fields ────────────────────────────────────────────────────
function onServiceChange(serviceSelect, formWrap, prefillData) {
    formWrap.innerHTML = '';
    formWrap.classList.add('hidden');
    const serviceId = serviceSelect.value;
    if (!serviceId) return;

    fetch(SERVICE_FORM_URL + '?service_id=' + serviceId)
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.html) return;
            formWrap.innerHTML = data.html;
            // Apply consistent styling
            formWrap.querySelectorAll('label').forEach(l => l.classList.add('block','text-xs','font-semibold','text-text-secondary','uppercase','tracking-wide','mb-1.5'));
            formWrap.querySelectorAll('input,select,textarea').forEach(el => el.className = FIELD_CLASSES);
            formWrap.classList.remove('hidden');

            // Pre-fill from saved data
            if (prefillData && Array.isArray(prefillData)) {
                prefillData.forEach(field => {
                    const name = field.name ?? field.label ?? '';
                    // Try to find by matching label text → input name
                    formWrap.querySelectorAll('input,select,textarea').forEach(el => {
                        const label = formWrap.querySelector('label[for="' + el.id + '"]');
                        if (label && label.textContent.trim().toLowerCase() === name.toLowerCase()) {
                            el.value = field.value ?? '';
                        }
                    });
                });
            }
        })
        .catch(() => {});
}

// ── Collect service form data before submit ──────────────────────────────────
function collectServiceFormData(formWrap, hiddenInput) {
    const fields = [];
    formWrap.querySelectorAll('input,select,textarea').forEach(el => {
        if (!el.name) return;
        const label = formWrap.querySelector('label[for="' + el.id + '"]');
        fields.push({
            name:  label ? label.textContent.trim() : el.name,
            value: el.value,
            type:  el.type || el.tagName.toLowerCase(),
        });
    });
    hiddenInput.value = JSON.stringify(fields);
}

// ── ADD modal wiring ─────────────────────────────────────────────────────────
const addCountry   = document.getElementById('addCountrySelect');
const addMethod    = document.getElementById('addMethodSelect');
const addMethodW   = document.getElementById('addMethodWrap');
const addService   = document.getElementById('addServiceSelect');
const addServiceW  = document.getElementById('addServiceWrap');
const addFormW     = document.getElementById('addServiceFormWrap');
const addFormData  = document.getElementById('addServiceFormData');

addCountry.addEventListener('change', () => onCountryChange(addCountry, addMethod, addMethodW, addService, addServiceW, addFormW));
addMethod.addEventListener('change', () => onMethodChange(addCountry, addMethod, addService, addServiceW, addFormW, null));
addService.addEventListener('change', () => onServiceChange(addService, addFormW, null));

document.getElementById('addRecipientForm').addEventListener('submit', function() {
    collectServiceFormData(addFormW, addFormData);
});

// ── EDIT modal ───────────────────────────────────────────────────────────────
const editCountry  = document.getElementById('editCountrySelect');
const editMethod   = document.getElementById('editMethodSelect');
const editMethodW  = document.getElementById('editMethodWrap');
const editService  = document.getElementById('editServiceSelect');
const editServiceW = document.getElementById('editServiceWrap');
const editFormW    = document.getElementById('editServiceFormWrap');
const editFormData = document.getElementById('editServiceFormData');

editCountry.addEventListener('change', () => onCountryChange(editCountry, editMethod, editMethodW, editService, editServiceW, editFormW));
editMethod.addEventListener('change', () => onMethodChange(editCountry, editMethod, editService, editServiceW, editFormW, null));
editService.addEventListener('change', () => onServiceChange(editService, editFormW, null));

document.getElementById('editRecipientForm').addEventListener('submit', function() {
    collectServiceFormData(editFormW, editFormData);
});

function openEditModal(id) {
    const rep = RECIPIENTS_DATA[id];
    if (!rep) return;

    // Set form action
    document.getElementById('editRecipientForm').action = '/user/recipients/' + id;

    // Basic fields
    document.getElementById('editName').value    = rep.name    || '';
    document.getElementById('editMobile').value  = rep.mobile  || '';
    document.getElementById('editEmail').value   = rep.email   || '';
    document.getElementById('editAddress').value = rep.address || '';

    // Reset dependent selects
    editMethod.innerHTML  = '<option value="">— Select method —</option>';
    editService.innerHTML = '<option value="">— Select service —</option>';
    editMethodW.classList.add('hidden');
    editServiceW.classList.add('hidden');
    editFormW.classList.add('hidden');
    editFormW.innerHTML = '';

    // Pre-select country
    if (rep.country_id) {
        editCountry.value = rep.country_id;
        // Populate delivery methods
        onCountryChange(editCountry, editMethod, editMethodW, editService, editServiceW, editFormW);
        // Pre-select method (after options are populated synchronously)
        if (rep.delivery_method_id) {
            editMethod.value = rep.delivery_method_id;
            // Load services, then pre-select service + form
            onMethodChange(editCountry, editMethod, editService, editServiceW, editFormW, rep.service_id);
            // If no service but has form data, pre-fill form after service loads
        }
    } else {
        editCountry.value = '';
    }

    // Store existing service_form_data to pre-fill after service form loads
    // (handled inside onMethodChange → onServiceChange via preselectServiceId)
    // We patch the service change to also carry prefill data
    if (rep.service_id && rep.service_form_data) {
        const savedData = rep.service_form_data;
        const origOnServiceChange = onServiceChange;
        // One-time override for this edit open
        const patchedListener = function() {
            onServiceChange(editService, editFormW, savedData);
        };
        editService.removeEventListener('change', patchedListener);
        editService.addEventListener('change', patchedListener, { once: true });
    }

    openModal('editRecipientModal');
}

function deleteRecipient(id) {
    if (!confirm('Delete this recipient?')) return;
    fetch('/user/recipients/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(() => alert('Delete failed'));
}

// Page init
window.addEventListener('DOMContentLoaded', () => window.initSidebar?.());
</script>
@endsection
