<div class="dashboard-section d-none" id="section-addresses">
    <div class="glass-card p-4 section-fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-white m-0"><i class="fas fa-map-marker-alt text-accent me-2"></i> عناوين الفوترة</h5>
            <button class="btn btn-sm btn-accent rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-1"></i> إضافة عنوان
            </button>
        </div>
        <div class="row g-3">
            @forelse($addresses as $address)
            <div class="col-md-6">
                <div class="glass-panel p-4 position-relative h-100">
                    @if($address->is_default)
                    <span class="badge bg-accent position-absolute top-0 start-0 m-3">الافتراضي</span>
                    @endif
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-glass rounded-circle" style="width:32px;height:32px;padding:0;"
                                data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}" title="تعديل">
                            <i class="fas fa-edit small"></i>
                        </button>
                        <form method="POST" action="{{ route('frontend.account.addresses.destroy', $address) }}" class="d-inline" onsubmit="return confirm('حذف هذا العنوان؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-glass rounded-circle text-danger" style="width:32px;height:32px;padding:0;">
                                <i class="fas fa-trash small"></i>
                            </button>
                        </form>
                    </div>
                    <h6 class="fw-bold text-white mb-2">{{ $address->name ?? $user->name }}</h6>
                    <p class="text-secondary small mb-1">{{ $address->address_line_1 }}</p>
                    @if($address->address_line_2)
                    <p class="text-secondary small mb-1">{{ $address->address_line_2 }}</p>
                    @endif
                    <p class="text-secondary small mb-2">{{ $address->city }}@if($address->country), {{ $address->country }}@endif</p>
                    @if($address->phone)
                    <p class="text-secondary small mb-0 en-text"><i class="fas fa-phone me-1"></i> {{ $address->phone }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4 text-secondary">
                <p class="mb-3">لم تُضف عناوين بعد.</p>
                <button type="button" class="btn btn-accent rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addAddressModal">إضافة عنوان</button>
            </div>
            @endforelse
        </div>
    </div>
</div>

@foreach($addresses as $address)
@include('frontend.pages.account.partials.address-edit-modal', ['address' => $address])
@endforeach
