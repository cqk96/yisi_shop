@php
    $locale = app()->getLocale();
    $locales = ['es', 'en', 'zh_CN'];
@endphp

<label class="language-switcher">
    <span>{{ __('ui.language') }}</span>
    <select onchange="if (this.value) window.location.href = this.value;">
        @foreach ($locales as $item)
            <option value="{{ route('locale.switch', $item) }}" {{ $locale === $item ? 'selected' : '' }}>
                {{ __('ui.languages.' . $item) }}
            </option>
        @endforeach
    </select>
</label>
