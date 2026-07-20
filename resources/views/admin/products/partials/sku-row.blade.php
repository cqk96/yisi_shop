<tr data-sku-row>
    <td>
        <input type="hidden" name="skus[{{ $index }}][id]" value="{{ $sku['id'] ?? '' }}" data-sku-name="id">
        <input type="hidden" name="skus[{{ $index }}][image_url]" value="{{ $sku['image_url'] ?? '' }}" data-sku-name="image_url">
        <input name="skus[{{ $index }}][name]" value="{{ $sku['name'] ?? '' }}" placeholder="{{ __('ui.admin.sku_name_placeholder') }}" data-sku-name="name">
    </td>
    <td><input name="skus[{{ $index }}][code]" value="{{ $sku['code'] ?? '' }}" placeholder="{{ __('ui.admin.sku_code_placeholder') }}" data-sku-name="code"></td>
    <td>
        <div class="sku-image-field">
            <label class="sku-image-picker">
                @if (! empty($sku['image_url']))
                    <img class="sku-image-preview" src="{{ $sku['image_url'] }}" alt="{{ $sku['name'] ?? '' }}">
                @else
                    <span class="sku-image-empty">
                        <span>{{ __('ui.admin.click_select_sku_image') }}</span>
                    </span>
                @endif
                <input
                    type="file"
                    name="sku_image_files[{{ $index }}]"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    data-sku-image-input
                    data-sku-file
                >
            </label>
            @if (! empty($sku['image_url']))
                <label class="sku-remove-image">
                    <input type="checkbox" name="skus[{{ $index }}][remove_image]" value="1" data-sku-name="remove_image">
                    {{ __('ui.admin.remove_sku_image') }}
                </label>
            @endif
        </div>
    </td>
    <td><input type="number" min="0" name="skus[{{ $index }}][stock]" value="{{ $sku['stock'] ?? 0 }}" data-sku-name="stock"></td>
    <td>
        <div class="sku-actions-cell">
            <label style="align-items: center; display: flex; gap: 8px; font-weight: 400;">
                <input type="checkbox" name="skus[{{ $index }}][is_active]" value="1" style="width: auto;" data-sku-name="is_active" {{ ! empty($sku['is_active']) ? 'checked' : '' }}>
                {{ __('ui.admin.active') }}
            </label>
            <button class="button secondary sku-row-remove" type="button" data-remove-sku-row>{{ __('ui.common.remove') }}</button>
        </div>
    </td>
</tr>
