@php $address = $address ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label text-secondary small">نوع العنوان</label>
        <select name="type" class="form-select bg-glass text-white border-secondary" required>
            <option value="billing" @selected(old('type', $address?->type) === 'billing')>فوترة</option>
            <option value="shipping" @selected(old('type', $address?->type) === 'shipping')>شحن</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">الاسم الكامل</label>
        <input type="text" name="name" class="form-control bg-glass text-white border-secondary" value="{{ old('name', $address?->name) }}" placeholder="الاسم">
    </div>
    <div class="col-12">
        <label class="form-label text-secondary small">رقم الهاتف</label>
        <input type="tel" name="phone" class="form-control bg-glass text-white border-secondary" value="{{ old('phone', $address?->phone) }}">
    </div>
    <div class="col-12">
        <label class="form-label text-secondary small">العنوان</label>
        <input type="text" name="address_line_1" class="form-control bg-glass text-white border-secondary" value="{{ old('address_line_1', $address?->address_line_1) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label text-secondary small">تفاصيل إضافية</label>
        <input type="text" name="address_line_2" class="form-control bg-glass text-white border-secondary" value="{{ old('address_line_2', $address?->address_line_2) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">المدينة</label>
        <input type="text" name="city" class="form-control bg-glass text-white border-secondary" value="{{ old('city', $address?->city) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">الدولة (رمز)</label>
        <input type="text" name="country" class="form-control bg-glass text-white border-secondary" value="{{ old('country', $address?->country ?? 'SA') }}" maxlength="2" placeholder="SA">
    </div>
    <div class="col-12">
        <label class="form-label text-secondary small">الرمز البريدي</label>
        <input type="text" name="postal_code" class="form-control bg-glass text-white border-secondary" value="{{ old('postal_code', $address?->postal_code) }}">
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input bg-glass border-secondary" type="checkbox" name="is_default" value="1" id="defaultAddress{{ $address?->id ?? 'new' }}" @checked(old('is_default', $address?->is_default))>
            <label class="form-check-label text-secondary" for="defaultAddress{{ $address?->id ?? 'new' }}">اجعله العنوان الافتراضي</label>
        </div>
    </div>
</div>
