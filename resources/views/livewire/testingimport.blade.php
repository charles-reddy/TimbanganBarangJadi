<div>
    {{-- START OF ERROR MESSAGE --}}
    @if ($errors->any())
    <div class="pt-3">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
        
    @endif


    @if (session()->has('message'))
    <div class="pt-3">
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="pt-3">
        <div class="alert alert-danger">
            <span class="sr-only">WARNING</span>
            <div>
            <span class="font-medium">Danger alert!</span> {{ session("error") }} 
            </div>
        </div>
    </div>
    @endif
    {{-- END OF ERROR MESSAGE --}}

    {{-- <form action="" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="suppliers">
        <input class="btn btn-primary" type="submit" value="import"  wire:click="import_file()">
    </form> --}}


            <form wire:submit.prevent="import_file">
                <input type="file" wire:model="suppliers" id="suppliers">
                <button type="submit">Import</button>
                @error('suppliers') <span class="error">{{ $message }}</span> @enderror
            </form>
</div>
