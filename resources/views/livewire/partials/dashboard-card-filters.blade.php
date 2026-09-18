<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label for="{{ $dateId }}" class="form-label small text-muted">{{ $dateLabel }}</label>
        <input type="date" id="{{ $dateId }}" class="form-control" wire:model.live="{{ $dateModel }}">
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label for="platno" class="form-label small text-muted">Plat No</label>
        <input type="text" id="platno" class="form-control" placeholder="Search plat no..."
            wire:model.live="katakunci">
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label for="customer" class="form-label small text-muted">Customer</label>
        <input type="text" id="customer" class="form-control" placeholder="Search customer..."
            wire:model.live="katacust">
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label for="sppb" class="form-label small text-muted">SPPB</label>
        <input type="text" id="sppb" class="form-control" placeholder="Search SPPB..."
            wire:model.live="katasppb">
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label for="product" class="form-label small text-muted">Product</label>
        <select id="product" class="form-select" multiple wire:model.live="kataproduct" size="1">
            @foreach ($products as $product)
                <option value="{{ $product->itemCode }}">{{ $product->itemName }}</option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
    </div>
</div>
