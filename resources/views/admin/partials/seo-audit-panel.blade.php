@php
    $seoAuditType = $seoAuditType ?? 'product';
    $aiModels = $aiModels ?? collect();
@endphp
<div class="seo-audit-panel border rounded p-3 mb-3 bg-light" data-seo-audit-type="{{ $seoAuditType }}">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <strong class="small"><i class="bi bi-clipboard-check text-primary"></i> فحص SEO</strong>
    @if($aiModels->isNotEmpty())
      <select class="form-select form-select-sm seo-audit-model" style="max-width: 200px;">
        @foreach($aiModels as $m)
          <option value="{{ $m->id }}" @selected($m->is_default)>{{ $m->name }}</option>
        @endforeach
      </select>
    @endif
  </div>
  <div class="d-flex flex-wrap gap-2 mb-2">
    <button type="button" class="btn btn-sm btn-primary seo-audit-run">
      <i class="bi bi-search"></i> فحص SEO
    </button>
    <button type="button" class="btn btn-sm btn-success seo-audit-apply" disabled>
      <i class="bi bi-magic"></i> تطبيق التوصيات بالذكاء الاصطناعي
    </button>
  </div>
  <div class="seo-audit-status small text-muted mb-2"></div>

  <div class="seo-audit-report d-none">
    <div class="d-flex align-items-center gap-3 mb-3">
      <div class="seo-audit-score-circle rounded-circle d-flex align-items-center justify-content-center fw-bold"
           style="width:64px;height:64px;border:3px solid var(--bs-primary);font-size:1.1rem;">—</div>
      <div class="flex-grow-1">
        <div class="seo-audit-summary small"></div>
        <div class="seo-audit-ai-summary small text-muted mt-1"></div>
      </div>
    </div>
    <div class="seo-audit-checks mb-2" style="max-height:220px;overflow-y:auto;"></div>
    <div class="seo-audit-recommendations small"></div>
  </div>
</div>

@once
  @push('scripts')
  <script>
  (function () {
    const auditUrl = @json(route('admin.ai.seo.audit'));
    const applyUrl = @json(route('admin.ai.seo.apply'));
    const csrf = @json(csrf_token());

    let lastReport = null;

    function panelFrom(el) {
      return el.closest('.seo-audit-panel');
    }

    function formField(name) {
      return document.querySelector('#product-edit-form [name="' + name + '"]')
        || document.querySelector('[name="' + name + '"]');
    }

    function fieldMap(panel) {
      const type = panel.dataset.seoAuditType || 'product';
      const isBlog = type === 'blog_post';
      const productOrPostName = formField('title')?.value?.trim()
          || formField('name')?.value?.trim() || '';
      const payload = {
        type: type,
        title: productOrPostName,
        name: productOrPostName,
        slug: formField('slug')?.value?.trim() || '',
        meta_title: formField('meta_title')?.value?.trim() || '',
        meta_description: formField('meta_description')?.value?.trim() || '',
        meta_keywords: formField('meta_keywords')?.value?.trim() || '',
        ai_model_id: panel.querySelector('.seo-audit-model')?.value || null,
      };
      if (isBlog) {
        payload.content = formField('content')?.value || '';
        if (window.tinymce?.get('content')) {
          payload.content = window.tinymce.get('content').getContent();
        }
        payload.excerpt = formField('excerpt')?.value?.trim() || '';
        payload.focus_keyword = formField('focus_keyword')?.value?.trim() || '';
        payload.featured_image_alt = formField('featured_image_alt')?.value?.trim() || '';
        payload.canonical_url = formField('canonical_url')?.value?.trim() || '';
      } else {
        payload.description = formField('description')?.value || '';
        if (window.tinymce?.get('description')) {
          payload.description = window.tinymce.get('description').getContent();
        }
        payload.short_description = formField('short_description')?.value?.trim() || '';
      }
      return payload;
    }

    function setStatus(panel, msg, isError) {
      const el = panel.querySelector('.seo-audit-status');
      if (!el) return;
      el.textContent = msg;
      el.className = 'seo-audit-status small ' + (isError ? 'text-danger' : 'text-muted');
    }

    function severityClass(sev) {
      if (sev === 'critical') return 'danger';
      if (sev === 'warning') return 'warning';
      return 'info';
    }

    function renderReport(panel, data) {
      lastReport = data;
      const report = panel.querySelector('.seo-audit-report');
      report?.classList.remove('d-none');

      const score = data.score ?? 0;
      const circle = panel.querySelector('.seo-audit-score-circle');
      if (circle) {
        circle.textContent = score;
        const color = score >= 80 ? 'var(--bs-success)' : (score >= 50 ? 'var(--bs-warning)' : 'var(--bs-danger)');
        circle.style.borderColor = color;
      }

      const sum = panel.querySelector('.seo-audit-summary');
      if (sum) sum.textContent = data.summary_ar || '';

      const aiSum = panel.querySelector('.seo-audit-ai-summary');
      if (aiSum) aiSum.textContent = data.ai?.overall_summary || '';

      const checksEl = panel.querySelector('.seo-audit-checks');
      if (checksEl) {
        checksEl.innerHTML = (data.checks || []).filter(c => !String(c.id).endsWith('_ok')).map(function (c) {
          return '<div class="alert alert-' + severityClass(c.severity) + ' py-1 px-2 mb-1 small">'
            + '<strong>' + c.message + '</strong><br><span class="text-muted">' + c.recommendation + '</span></div>';
        }).join('') || '<p class="text-muted small mb-0">لا توجد مشاكل حرجة.</p>';
      }

      const recEl = panel.querySelector('.seo-audit-recommendations');
      const recs = data.recommendations || data.ai?.prioritized_recommendations || [];
      if (recEl) {
        recEl.innerHTML = '<strong>توصيات AI:</strong><ul class="mb-0 ps-3">'
          + recs.map(r => '<li><strong>' + (r.title || '') + '</strong>: ' + (r.detail || '') + '</li>').join('')
          + '</ul>';
      }

      panel.querySelector('.seo-audit-apply')?.removeAttribute('disabled');
    }

    function setFieldValue(name, value) {
      const el = document.querySelector('#product-edit-form [name="' + name + '"]')
        || document.querySelector('[name="' + name + '"]');
      if (!el || value == null || value === '') return;
      el.value = value;
      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function fillFields(panel, fields) {
      const type = panel.dataset.seoAuditType || 'product';
      const map = ['meta_title', 'meta_description', 'meta_keywords', 'slug', 'focus_keyword', 'excerpt'];
      map.forEach(function (key) {
        if (!fields[key]) return;
        setFieldValue(key, fields[key]);
      });
      if (fields.excerpt && type === 'blog_post') {
        const ex = document.querySelector('[name="excerpt"]');
        if (ex) ex.value = fields.excerpt;
      }
      if (fields.description || fields.content) {
        const id = type === 'blog_post' ? 'content' : 'description';
        const ed = window.tinymce?.get(id);
        if (ed && fields[id]) ed.setContent(fields[id]);
      }
      const scoreInput = document.querySelector('[name="seo_score"]');
      if (scoreInput && lastReport?.score != null) scoreInput.value = lastReport.score;
    }

    async function post(url, body) {
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify(body),
      });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error(data.message || 'فشل الطلب');
      return data.data;
    }

    document.querySelectorAll('.seo-audit-run').forEach(function (btn) {
      btn.addEventListener('click', async function () {
        const panel = panelFrom(this);
        const payload = fieldMap(panel);
        if (!payload.title) {
          setStatus(panel, 'أدخل العنوان/الاسم أولاً', true);
          return;
        }
        setStatus(panel, 'جاري فحص SEO...');
        this.disabled = true;
        panel.querySelector('.seo-audit-apply')?.setAttribute('disabled', 'disabled');
        try {
          const data = await post(auditUrl, payload);
          renderReport(panel, data);
          setStatus(panel, 'اكتمل الفحص — راجع التقرير ثم طبّق التوصيات إن رغبت.');
        } catch (e) {
          setStatus(panel, e.message, true);
        } finally {
          this.disabled = false;
        }
      });
    });

    document.querySelectorAll('.seo-audit-apply').forEach(function (btn) {
      btn.addEventListener('click', async function () {
        const panel = panelFrom(this);
        if (!lastReport) return;
        const recs = lastReport.recommendations || lastReport.ai?.prioritized_recommendations || [];
        const payload = fieldMap(panel);
        payload.recommendations = recs;
        setStatus(panel, 'جاري تطبيق التوصيات على الحقول...');
        this.disabled = true;
        try {
          const fields = await post(applyUrl, payload);
          fillFields(panel, fields);
          setStatus(panel, 'تم التطبيق — جاري إعادة الفحص بالحقول المحدّثة...');
          const refreshed = fieldMap(panel);
          refreshed.recommendations = recs;
          const data = await post(auditUrl, refreshed);
          renderReport(panel, data);
          setStatus(panel, 'تم التطبيق وإعادة الفحص — اضغط «حفظ التعديلات» أسفل الصفحة لحفظ SEO في قاعدة البيانات.', false);
        } catch (e) {
          setStatus(panel, e.message, true);
        } finally {
          this.disabled = false;
        }
      });
    });
  })();
  </script>
  @endpush
@endonce
