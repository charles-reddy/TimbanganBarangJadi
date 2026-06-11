<div class="container-fluid px-4">
    <!-- Alert Messages -->
    @if (session('error'))
        <div class="pt-3 animate__animated animate__fadeIn">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="pt-3 animate__animated animate__fadeIn">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Perhatian!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="pt-3 animate__animated animate__fadeIn">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mt-4 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-box-open fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">Master Product</h3>
                            <small>Kelola Data Produk</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Input -->
    <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInUp">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2 text-primary"></i>
                {{ $updateData ? 'Edit Data Produk' : 'Tambah Data Produk' }}
            </h5>
        </div>
        <div class="card-body p-4">
            <form wire:submit.prevent="{{ $updateData ? 'update' : 'store' }}">
                <div class="row g-3">
                    <!-- Item Code -->
                    <div class="col-md-6">
                        <label for="itemCode" class="form-label fw-semibold">
                            <i class="fas fa-barcode me-1 text-primary"></i>
                            Kode Item <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('itemCode') is-invalid @enderror"
                            id="itemCode" wire:model="itemCode" placeholder="Masukkan kode item"
                            {{ $updateData ? 'disabled' : '' }}>
                        @error('itemCode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <label for="type" class="form-label fw-semibold">
                            <i class="fas fa-tag me-1 text-primary"></i>
                            Tipe Produk <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type"
                            wire:model="type">
                            <option value="">Pilih Tipe</option>
                            <option value="FG">FG (Finished Goods)</option>
                            <option value="FG-L">FG-L (Finished Goods - Liquid)</option>
                            <option value="NFG">NFG (Non Finished Goods)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- UOM -->
                    <div class="col-md-6">
                        <label for="uom" class="form-label fw-semibold">
                            <i class="fas fa-balance-scale me-1 text-primary"></i>
                            UOM (Unit of Measure)
                        </label>
                        <input type="text" class="form-control" id="uom" wire:model="uom"
                            placeholder="Contoh: Kg, Liter, Pcs">
                    </div>

                    <!-- Item Name -->
                    <div class="col-12">
                        <label for="itemName" class="form-label fw-semibold">
                            <i class="fas fa-cube me-1 text-primary"></i>
                            Nama Produk <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('itemName') is-invalid @enderror"
                            id="itemName" wire:model="itemName" placeholder="Masukkan nama produk">
                        @error('itemName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 flex-wrap">
                            @if ($updateData == false)
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            @else
                                <button type="submit" class="btn btn-success px-4"
                                    wire:confirm="Yakin ingin menyimpan perubahan?">
                                    <i class="fas fa-check me-2"></i>Update
                                </button>
                            @endif
                            <button type="button" class="btn btn-secondary px-4" wire:click="clear()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Daftar Produk
                    </h5>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control"
                            placeholder="Cari berdasarkan kode, nama, tipe, atau UOM..." wire:model.live="katakunci">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th class="sort {{ $sortColumn == 'itemCode' ? $sortDirection : '' }}"
                                wire:click="sort('itemCode')" style="cursor: pointer;">
                                <i class="fas fa-barcode me-1"></i>Kode Item
                                @if ($sortColumn == 'itemCode')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th class="sort {{ $sortColumn == 'itemName' ? $sortDirection : '' }}"
                                wire:click="sort('itemName')" style="cursor: pointer;">
                                <i class="fas fa-cube me-1"></i>Nama Produk
                                @if ($sortColumn == 'itemName')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th class="sort {{ $sortColumn == 'type' ? $sortDirection : '' }}"
                                wire:click="sort('type')" style="cursor: pointer;">
                                <i class="fas fa-tag me-1"></i>Tipe
                                @if ($sortColumn == 'type')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th class="sort {{ $sortColumn == 'uom' ? $sortDirection : '' }}"
                                wire:click="sort('uom')" style="cursor: pointer;">
                                <i class="fas fa-balance-scale me-1"></i>UOM
                                @if ($sortColumn == 'uom')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @endif
                            </th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mproduct as $key => $value)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="badge bg-light text-dark">
                                        {{ $mproduct->firstItem() + $key }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <strong class="text-primary">{{ $value->itemCode }}</strong>
                                </td>
                                <td class="align-middle">{{ $value->itemName }}</td>
                                <td class="align-middle">
                                    @if ($value->type == 'FG-L')
                                        <span class="badge bg-info">{{ $value->type }}</span>
                                    @elseif($value->type == 'FG')
                                        <span class="badge bg-success">{{ $value->type }}</span>
                                    @elseif($value->type == 'NFG')
                                        <span class="badge bg-warning text-dark">{{ $value->type }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $value->type }}</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $value->uom ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <button wire:click="edit('{{ $value->itemCode }}')"
                                        class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Tidak ada data produk</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan {{ $mproduct->firstItem() ?? 0 }} - {{ $mproduct->lastItem() ?? 0 }}
                    dari {{ $mproduct->total() }} data
                </div>
                <div>
                    {{ $mproduct->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table th.sort:hover {
            background-color: #e9ecef;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .animate__animated {
            animation-duration: 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeIn {
            animation-name: fadeIn;
        }

        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
    </style>
</div>
