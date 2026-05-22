<div class="modal fade quick-view-modal" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-map-marker-alt text-accent me-2"></i> إضافة عنوان جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('frontend.account.addresses.store') }}">
                @csrf
                <div class="modal-body">
                    @include('frontend.pages.account.partials.address-form-fields')
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-glass px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-accent px-4">حفظ العنوان</button>
                </div>
            </form>
        </div>
    </div>
</div>
