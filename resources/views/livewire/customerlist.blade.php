<div>
    {{-- START OF ERROR MESSAGE --}}
    @if (session()->has('message'))
        <div class="pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    {{-- END OF ERROR MESSAGE --}}

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <!-- Header Card -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">
                    <i class="bi bi-people-fill me-2"></i>Daftar Customer
                </h3>
                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Kelola dan Reset Password Customer</p>
            </div>
        </div>

        <!-- Search Box -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Cari Customer
                        </label>
                        <input type="text" class="form-control" id="search" wire:model.live="search"
                            placeholder="Ketik nama customer...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-info-circle"></i> Total Data
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="bi bi-database-fill"></i>
                            </span>
                            <input type="text" class="form-control fw-bold"
                                value="{{ $datacustomer->total() }} Customer" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Data Customer
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 5%;">No</th>
                                <th scope="col" style="width: 30%;">Nama Customer</th>
                                <th scope="col" style="width: 35%;">Email</th>
                                <th scope="col" class="text-center" style="width: 30%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($datacustomer as $index => $customer)
                                <tr>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary">
                                            {{ ($datacustomer->currentPage() - 1) * $datacustomer->perPage() + $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px; font-weight: bold;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $customer->name }}</div>
                                                <small class="text-muted">ID: {{ $customer->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @if ($customer->email)
                                            <i class="bi bi-envelope-fill text-primary"></i>
                                            {{ $customer->email }}
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="if(confirm('Yakin ingin reset password untuk {{ $customer->name }}?\n\nPassword akan direset ke: ktm{{ date('Y') }}')) { @this.call('resetPassword', {{ $customer->id }}) }">
                                            <i class="bi bi-key-fill"></i> Reset Password
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">Tidak ada customer ditemukan</p>
                                            <small>Coba ubah kriteria pencarian</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($datacustomer->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $datacustomer->firstItem() }} - {{ $datacustomer->lastItem() }} dari
                            {{ $datacustomer->total() }} data
                        </div>
                        <div>
                            {{ $datacustomer->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
