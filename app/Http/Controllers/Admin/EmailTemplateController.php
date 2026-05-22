<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmailTemplateRequest;
use App\Http\Requests\Admin\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('event')) {
            $query->where('event', $request->string('event'));
        }

        if ($request->filled('locale')) {
            $query->where('locale', $request->string('locale'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('key', 'like', '%' . $search . '%');
            });
        }

        $templates = $query->orderBy('event')
            ->orderBy('locale')
            ->paginate(15)
            ->withQueryString();

        $events = EmailTemplate::events();

        return view('admin.email-templates.index', compact('templates', 'events'));
    }

    public function create()
    {
        $events = EmailTemplate::events();
        $locales = ['ar' => 'العربية', 'en' => 'English'];

        return view('admin.email-templates.create', compact('events', 'locales'));
    }

    public function store(StoreEmailTemplateRequest $request)
    {
        EmailTemplate::create($request->validated());

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'تم إنشاء قالب البريد الإلكتروني بنجاح.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $events = EmailTemplate::events();
        $locales = ['ar' => 'العربية', 'en' => 'English'];

        return view('admin.email-templates.edit', [
            'template' => $emailTemplate,
            'events' => $events,
            'locales' => $locales,
        ]);
    }

    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        $emailTemplate->update($request->validated());

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'تم تحديث قالب البريد الإلكتروني بنجاح.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'تم حذف قالب البريد الإلكتروني بنجاح.');
    }
}

