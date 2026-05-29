<div class="modal fade quick-view-modal account-address-modal" id="editAddressModal{{ $address->id }}" tabindex="-1" aria-labelledby="editAddressModalLabel{{ $address->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header account-address-modal__header">
                <h5 class="modal-title account-address-modal__title" id="editAddressModalLabel{{ $address->id }}">
                    <i class="fas fa-edit me-2" aria-hidden="true"></i> تعديل العنوان
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form method="POST" action="{{ route('frontend.account.addresses.update', $address) }}" class="account-address-modal__form">
                @csrf
                @method('PATCH')
                <div class="modal-body account-address-modal__body">
                    @include('frontend.pages.account.partials.address-form-fields', [
                        'address' => $address,
                        'formKey' => $address->id,
                    ])
                </div>
                <div class="modal-footer account-address-modal__footer">
                    <button type="button" class="btn btn-glass px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-accent account-profile-form__submit px-4">
                        <i class="fas fa-check me-2" aria-hidden="true"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
