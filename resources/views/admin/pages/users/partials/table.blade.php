<div class="table-responsive">
    <table class="table table-striped table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col" style="width: 40px;">#</th>
                <th scope="col" style="min-width: 150px;">اسم المستخدم</th>
                <th scope="col" style="min-width: 200px;">البريد</th>
                <th scope="col" style="min-width: 120px;">الهاتف</th>
                <th scope="col" style="min-width: 130px;">اخر دخول</th>
                <th scope="col" style="min-width: 150px;">الأدوار</th>
                <th scope="col" style="min-width: 110px;">الحالة</th>
                <th scope="col" style="min-width: 120px;">الحالة النشطة</th>
                <th scope="col" style="min-width: 200px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php
                    $userSessions = $sessions->get($user->id);
                    $lastSession = $userSessions ? $userSessions->first() : null;
                @endphp
                <tr>
                    <th scope="row">{{ $users->firstItem() + $loop->index }}</th>
                    <td>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-decoration-none">
                            {{ $user->name }}
                        </a>
                    </td>
                    <td>
                        @if ($user->email)
                            <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none" title="إرسال بريد إلكتروني">
                                {{ $user->email }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank"
                               class="text-success text-decoration-none me-1" title="فتح WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            {{ $user->phone }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($lastSession)
                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                        @else
                            لا توجد جلسات
                        @endif
                    </td>
                    <td>
                        @foreach ($user->getRoleNames() as $role)
                            <span class="badge bg-primary me-1">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if ($user->status === 'active')
                            <span class="badge bg-success">مفعل</span>
                        @elseif($user->status === 'inactive')
                            <span class="badge bg-warning text-dark">موقوف</span>
                        @elseif($user->status === 'banned')
                            <span class="badge bg-danger">محظور</span>
                        @else
                            <span class="badge bg-secondary">غير معروف</span>
                        @endif
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-sm btn-toggle-status {{ $user->is_active ? 'btn-success' : 'btn-danger' }}"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ e($user->name) }}"
                                data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                title="{{ $user->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </button>
                    </td>
                    <td>
                        <a class="btn btn-info btn-sm me-1" href="{{ route('admin.users.edit', $user->id) }}" title="تعديل المستخدم">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $user->id }}" title="حذف المستخدم">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#change_password{{ $user->id }}" title="تعديل كلمة السر">
                            <i class="fa-solid fa-key"></i>
                        </a>
                    </td>
                </tr>
                @include('admin.pages.users.delete')
                @include('admin.pages.users.change_password')
            @empty
                <tr>
                    <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($users->hasPages())
        <div class="mt-3" id="users-pagination">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
