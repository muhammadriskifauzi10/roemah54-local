@extends('templates.dashboard.main')

@section('contents')
    <div class="container-fluid mt-3 mb-3">
        <div class="row">
            <div class="col-xl-2 mb-3">
                @include('templates.dashboard.sidebar')
            </div>
            <div class="col-xl-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:history.back()">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Menu</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('menu.update', encrypt($menu->id)) }}" class="row" autocomplete="off"
                            method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                {{-- nama --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="nama" class="form-label fw-bold">Nama Menu</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                        name="nama" id="nama" placeholder="Masukkan Nama Menu"
                                        value="{{ old('nama', $menu->name) }}">
                                    @error('nama')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- route --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="route" class="form-label fw-bold">Route</label>
                                    <input type="text" class="form-control @error('route') is-invalid @enderror"
                                        name="route" id="route" placeholder="Masukkan route"
                                        value="{{ old('route', $menu->route) }}">
                                    @error('route')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row align-items-end">
                                {{-- referensi dari --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="referensi_dari" class="form-label fw-bold">Referensi Dari</label>
                                    <select class="form-select form-select-2 @error('referensi_dari') is-invalid @enderror"
                                        name="referensi_dari" id="referensi_dari" style="width: 100%;"
                                        onchange="onReferensiDari()">
                                        <option>Pilih Referensi</option>
                                        @foreach (\App\Models\Menu::where('id', '<>', $menu->id)->whereNull('parent_id')->orderBy('order', 'ASC')->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ $row->id == $menu->parent_id ? 'selected' : '' }}>{{ $row->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('referensi_dari')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- sub menu --}}
                                <div class="col-lg-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="submenu"
                                            {{ $menu->parent_id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="submenu">
                                            Sub Menu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- menu --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="menu" class="form-label fw-bold">Menu</label>
                                    <select class="form-select form-select-2 @error('menu') is-invalid @enderror"
                                        name="menu" id="menu" style="width: 100%;">
                                        <option>Pilih Menu</option>
                                    </select>
                                    @error('menu')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- after dan before --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="urutan" class="form-label fw-bold">Urutan</label>
                                    <select class="form-select form-select-2 @error('urutan') is-invalid @enderror"
                                        name="urutan" id="urutan" style="width: 100%;">
                                        <option>Pilih Urutan</option>
                                        <option value="-1">Sebelumnya</option>
                                        <option value="1">Setelahnya</option>
                                    </select>
                                    @error('urutan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                        <strong class="d-block">
                                            Perbarui
                                        </strong>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {
            $("#btn-submit").on("click", function() {
                $("#btn-submit").html(`
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `)

                setTimeout(function() {
                    $("#btn-submit").prop("disabled", true)
                }, 1);
            })

        })

        function onReferensiDari() {
            let referensi_dari = $("#referensi_dari").val();
            let submenu = $("#submenu").is(":checked");

            if (referensi_dari === "Pilih Referensi") {
                $("#submenu").prop("checked", false); // Uncheck the checkbox
            } else {
                $("#submenu").prop("checked",
                    true); // Check the checkbox if a different value is selected
            }

            $("#menu").empty()
            $("#menu").append(`
                <option>Pilih Menu</option>
            `)

            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));
            formData.append("referensi_dari", referensi_dari);
            formData.append("submenu", submenu);

            $.ajax({
                url: "{{ route('menu.getmenufromreferensidari') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#menu").append(response.data['dataHTML'])
                    }
                },
            });
        }
    </script>
@endpush
