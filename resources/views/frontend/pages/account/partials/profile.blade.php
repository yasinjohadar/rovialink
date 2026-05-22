<div class="dashboard-section d-none" id="section-profile">
    <div class="glass-card p-4 section-fade-up">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-user-edit text-accent me-2"></i> الملف الشخصي</h5>
        <form method="POST" action="{{ route('frontend.account.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label text-secondary small">صورة الحساب</label>
                    <input type="file" name="photo" class="form-control bg-glass text-white border-secondary" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small">الاسم</label>
                    <input type="text" name="name" class="form-control bg-glass text-white border-secondary @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small">رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-control bg-glass text-white border-secondary @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label text-secondary small">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control bg-glass text-white border-secondary @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr class="border-secondary border-opacity-25 my-4">
            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-accent px-4">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>
