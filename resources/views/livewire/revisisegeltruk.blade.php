<div>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    @if (session('message'))
        <div class="alert alert-success" role="alert">{{ session('message') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($transID)
        <section class="mb-4 p-3 bg-body rounded shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h3 class="h4 mb-1">Revisi Foto Segel</h3>
                    <p class="text-muted mb-0">Pilih hanya foto yang perlu diganti. Foto lainnya tidak akan berubah.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary" wire:click="batal">Kembali ke daftar</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">SPM</label>
                    <input type="text" class="form-control" value="{{ $spmNo }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Polisi</label>
                    <input type="text" class="form-control" value="{{ $carID }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Driver</label>
                    <input type="text" class="form-control" value="{{ $driver }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <input type="text" class="form-control" value="{{ $custName }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item</label>
                    <input type="text" class="form-control" value="{{ $itemName }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Truk</label>
                    <input type="text" class="form-control" value="{{ $jenisTruk }}" disabled>
                </div>
            </div>

            <form wire:submit="simpanRevisi">
                <div class="row g-3">
                    @foreach (range(1, 5) as $nomor)
                        @php
                            $fotoBaru = $this->{'fotoSealNo' . $nomor};
                            $nomorSeal = $this->{'sealNo' . $nomor};
                            $fotoSebelumnya = $fotoLama[$nomor] ?? null;
                        @endphp
                        <div class="col-xl-4 col-md-6" wire:key="foto-seal-{{ $transID }}-{{ $nomor }}">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Seal {{ $nomor }}</strong>
                                    <span class="text-muted">{{ $nomorSeal ?: 'Tanpa nomor seal' }}</span>
                                </div>
                                <div class="card-body">
                                    <p class="fw-semibold mb-2">Foto sebelumnya</p>
                                    @if ($fotoSebelumnya)
                                        <img class="img-thumbnail w-100 mb-3"
                                            style="height: 260px; object-fit: contain;"
                                            src="{{ asset('storage/' . ltrim($fotoSebelumnya, '/')) }}"
                                            alt="Foto sebelumnya Seal {{ $nomor }}">
                                    @else
                                        <div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted mb-3"
                                            style="height: 260px;">
                                            Belum ada foto
                                        </div>
                                    @endif

                                    <label for="fotoSealNo{{ $nomor }}" class="form-label fw-semibold">Foto
                                        pengganti</label>
                                    <input id="fotoSealNo{{ $nomor }}" type="file"
                                        class="form-control @error('fotoSealNo' . $nomor) is-invalid @enderror"
                                        accept="image/png,image/jpeg" wire:model="fotoSealNo{{ $nomor }}">
                                    @error('fotoSealNo' . $nomor)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div wire:loading wire:target="fotoSealNo{{ $nomor }}"
                                        class="text-primary mt-2">
                                        Mengunggah foto...
                                    </div>

                                    @if ($fotoBaru)
                                        <p class="fw-semibold mt-3 mb-2">Preview pengganti</p>
                                        <img class="img-thumbnail w-100" style="height: 260px; object-fit: contain;"
                                            src="{{ $fotoBaru->temporaryUrl() }}"
                                            alt="Preview pengganti Seal {{ $nomor }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                        wire:target="simpanRevisi,fotoSealNo1,fotoSealNo2,fotoSealNo3,fotoSealNo4,fotoSealNo5">
                        Simpan Revisi
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="batal">Batal</button>
                </div>
            </form>
        </section>
    @else
        <section class="p-3 bg-body rounded shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <h3 class="h4 mb-1">Truk Sudah Disegel</h3>
                    <p class="text-muted mb-0">Hanya transaksi hari ini dan kemarin yang dapat direvisi.</p>
                </div>
                <div style="min-width: 280px;">
                    <label for="pencarian-transaksi" class="form-label">Cari SPM atau nomor polisi</label>
                    <input id="pencarian-transaksi" type="search" class="form-control"
                        placeholder="Masukkan SPM atau nomor polisi" wire:model.live.debounce.400ms="katakunci">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal SPM</th>
                            <th>SPM</th>
                            <th>Driver</th>
                            <th>Nomor Polisi</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Jenis Truk</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $key => $item)
                            <tr wire:key="transaksi-segel-{{ $item->id }}">
                                <td>{{ $transaksi->firstItem() + $key }}</td>
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tglSpm)->format('d-m-Y H:i') }}
                                </td>
                                <td>{{ $item->spmNo }}</td>
                                <td>{{ $item->driver }}</td>
                                <td>{{ $item->carID }}</td>
                                <td>{{ $item->custName }}</td>
                                <td>{{ $item->itemName }}</td>
                                <td>{{ $item->jenisTruk }}</td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        wire:click="pilihTransaksi({{ $item->id }})">
                                        Revisi Foto
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Transaksi segel tidak
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transaksi->links() }}
        </section>
    @endif
</div>
