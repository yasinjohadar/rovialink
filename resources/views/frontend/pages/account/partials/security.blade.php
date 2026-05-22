<div class="dashboard-section d-none" id="section-security">
    <div class="glass-card p-4 mb-4 section-fade-up">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-lock text-accent me-2"></i> تغيير كلمة المرور</h5>
        <form method="POST" action="{{ route('frontend.account.password.update') }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label text-secondary small">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" class="form-control bg-glass text-white border-secondary @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small">كلمة المرور الجديدة</label>
                    <input type="password" name="password" class="form-control bg-glass text-white border-secondary @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control bg-glass text-white border-secondary" required>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-accent px-4">تحديث كلمة المرور</button>
            </div>
        </form>
    </div>

    <div class="glass-card p-4 section-fade-up">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-shield-alt text-accent me-2"></i> إعدادات الأمان</h5>
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 glass-panel rounded-3 opacity-75">
            <div>
                <h6 class="fw-bold text-white mb-1">المصادقة الثنائية</h6>
                <p class="text-secondary small mb-0">قريباً — طبقة حماية إضافية لحسابك</p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" disabled role="switch" style="width: 3em; height: 1.5em;">
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 glass-panel rounded-3 opacity-75">
            <div>
                <h6 class="fw-bold text-white mb-1">الأجهزة النشطة</h6>
                <p class="text-secondary small mb-0">قريباً — إدارة الأجهزة المتصلة بحسابك</p>
            </div>
            <button type="button" class="btn btn-sm btn-glass rounded-pill px-3" disabled>إدارة</button>
        </div>
    </div>
</div>
