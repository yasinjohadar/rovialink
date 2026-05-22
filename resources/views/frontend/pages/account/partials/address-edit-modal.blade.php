<div class="modal fade quick-view-modal" id="editAddressModal{{ $address->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white fw-bold">تعديل العنوان</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('frontend.account.addresses.update', $address) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    @include('frontend.pages.account.partials.address-form-fields', ['address' => $address])
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-glass px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-accent px-4">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
