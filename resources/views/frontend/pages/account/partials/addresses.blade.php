<div class="dashboard-section d-none" id="section-addresses">
    <div class="glass-card account-addresses-panel section-fade-up">
        <div class="account-addresses-panel__header">
            <div>
                <h5 class="account-panel__title mb-1">
                    <i class="fas fa-map-marker-alt me-2" aria-hidden="true"></i> عناوين الفوترة
                </h5>
                <p class="account-addresses-panel__subtitle mb-0">إدارة عناوين الفوترة المرتبطة بحسابك</p>
            </div>
            <button class="btn btn-accent rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-1" aria-hidden="true"></i> إضافة عنوان
            </button>
        </div>

        @if($addresses->isNotEmpty())
        <div class="account-addresses-grid">
            @foreach($addresses as $address)
            <article class="account-address-card">
                <div class="account-address-card__head">
                    <div class="account-address-card__identity">
                        <span class="account-address-card__icon" aria-hidden="true"><i class="fas fa-receipt"></i></span>
                        <h6 class="account-address-card__name">{{ $address->name ?? $user->name }}</h6>
                    </div>
                    <div class="account-address-card__actions">
                        <button type="button"
                                class="account-address-card__action"
                                data-bs-toggle="modal"
                                data-bs-target="#editAddressModal{{ $address->id }}"
                                title="تعديل">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                        </button>
                        <form method="POST"
                              action="{{ route('frontend.account.addresses.destroy', $address) }}"
                              class="d-inline"
                              onsubmit="return confirm('حذف هذا العنوان؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="account-address-card__action account-address-card__action--danger" title="حذف">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="account-address-card__body">
                    <p class="account-address-card__line">{{ $address->address_line_1 }}</p>
                    @if($address->address_line_2)
                    <p class="account-address-card__line account-address-card__line--muted">{{ $address->address_line_2 }}</p>
                    @endif
                    @if($address->city)
                    <p class="account-address-card__meta">
                        <i class="fas fa-city" aria-hidden="true"></i>
                        {{ $address->city }}
                    </p>
                    @endif
                    @if($address->phone)
                    <p class="account-address-card__meta en-text" dir="ltr">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        {{ $address->phone }}
                    </p>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="account-addresses-empty">
            <div class="account-addresses-empty__icon" aria-hidden="true"><i class="fas fa-map-marked-alt"></i></div>
            <h6 class="account-addresses-empty__title">لا توجد عناوين بعد</h6>
            <p class="account-addresses-empty__text">أضف عنوان فوترة لاستخدامه في طلباتك القادمة.</p>
            <button type="button" class="btn btn-accent rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-1" aria-hidden="true"></i> إضافة عنوان
            </button>
        </div>
        @endif
    </div>
</div>

@foreach($addresses as $address)
@include('frontend.pages.account.partials.address-edit-modal', ['address' => $address])
@endforeach
