<div class="dashboard-section d-none" id="section-profile">
    <div class="glass-card account-profile-card section-fade-up">
        <div class="account-profile-card__header">
            <div>
                <h5 class="account-panel__title mb-1">
                    <i class="fas fa-user-edit me-2" aria-hidden="true"></i> الملف الشخصي
                </h5>
                <p class="account-profile-card__subtitle mb-0">حدّث بياناتك وصورتك الشخصية</p>
            </div>
        </div>

        <form method="POST"
              action="{{ route('frontend.account.profile.update') }}"
              enctype="multipart/form-data"
              class="account-profile-form">
            @csrf
            @method('PATCH')

            <div class="account-profile-form__hero">
                <div class="account-profile-form__avatar-block">
                    <div class="account-profile-form__avatar-ring">
                        <div class="account-profile-form__avatar" id="accountPhotoPreview">
                            @if($user->photoUrl())
                                <img src="{{ $user->photoUrl() }}"
                                     alt="{{ $user->name }}"
                                     class="account-profile-form__avatar-img"
                                     id="accountPhotoPreviewImg"
                                     width="112"
                                     height="112">
                            @else
                                <span class="account-profile-form__avatar-initials" id="accountPhotoPreviewInitials">
                                    {{ \App\Http\Controllers\Frontend\AccountController::userInitials($user->name) }}
                                </span>
                            @endif
                        </div>
                        <label for="accountPhotoInput" class="account-profile-form__avatar-btn" title="تغيير الصورة">
                            <i class="fas fa-camera" aria-hidden="true"></i>
                        </label>
                    </div>
                    <input type="file"
                           name="photo"
                           id="accountPhotoInput"
                           class="account-profile-form__file-input @error('photo') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
                </div>
                <div class="account-profile-form__hero-meta">
                    <h6 class="account-profile-form__hero-name">{{ $user->name }}</h6>
                    <p class="account-profile-form__hero-email en-text">{{ $user->email }}</p>
                    <div class="account-profile-form__tags">
                        <span class="account-profile-form__tag"><i class="fas fa-image" aria-hidden="true"></i> JPG · PNG</span>
                        <span class="account-profile-form__tag"><i class="fas fa-weight-hanging" aria-hidden="true"></i> حتى 2MB</span>
                    </div>
                    <p class="account-profile-form__file-name" id="accountPhotoFileName" hidden></p>
                    @error('photo')<div class="account-profile-form__error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="account-profile-form__divider"></div>

            <div class="account-profile-form__section">
                <h6 class="account-profile-form__section-title">البيانات الشخصية</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="account-field__label" for="profileName">الاسم الكامل</label>
                        <div class="account-field">
                            <span class="account-field__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
                            <input type="text"
                                   id="profileName"
                                   name="name"
                                   class="account-field__input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="أدخل اسمك"
                                   required>
                        </div>
                        @error('name')<div class="account-profile-form__error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="account-field__label" for="profilePhone">رقم الهاتف</label>
                        <div class="account-field">
                            <span class="account-field__icon" aria-hidden="true"><i class="fas fa-phone"></i></span>
                            <input type="tel"
                                   id="profilePhone"
                                   name="phone"
                                   class="account-field__input en-text @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="05xxxxxxxx"
                                   dir="ltr">
                        </div>
                        @error('phone')<div class="account-profile-form__error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="account-field__label" for="profileEmail">البريد الإلكتروني</label>
                        <div class="account-field">
                            <span class="account-field__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                            <input type="email"
                                   id="profileEmail"
                                   name="email"
                                   class="account-field__input en-text @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="name@example.com"
                                   dir="ltr"
                                   required>
                        </div>
                        @error('email')<div class="account-profile-form__error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="account-profile-form__actions">
                <button type="submit" class="btn btn-accent account-profile-form__submit">
                    <i class="fas fa-check me-2" aria-hidden="true"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('accountPhotoInput');
    const preview = document.getElementById('accountPhotoPreview');
    const fileNameEl = document.getElementById('accountPhotoFileName');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) return;

        if (fileNameEl) {
            fileNameEl.hidden = false;
            fileNameEl.textContent = 'الملف المختار: ' + file.name;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            preview.innerHTML = '<img src="' + event.target.result + '" alt="" class="account-profile-form__avatar-img" width="112" height="112">';
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush
