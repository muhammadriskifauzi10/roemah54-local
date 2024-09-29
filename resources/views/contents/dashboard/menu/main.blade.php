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
                        <li class="breadcrumb-item active" aria-current="page">Menu</li>
                    </ol>
                </nav>

                {{-- <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalTambahRole()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Role</button>
                </div> --}}

                {{-- role --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableMenu" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Menu</th>
                                    <th scope="col">Route</th>
                                    <th scope="col">Referensi Route</th>
                                    <th scope="col">Urutan</th>
                                    <th scope="col" width="150"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        var tableMenu
        $(document).ready(function() {
            tableMenu = $("#datatableMenu").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('menu.datatablemenu') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json"
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "name",
                    },
                    {
                        data: "route",
                    },
                    {
                        data: "parent_id",
                    },
                    {
                        data: "order",
                    },
                    {
                        data: "aksi",
                    },
                ],
                // "order": [
                //     [1, 'asc']
                // ],
                // scrollY: "700px",
                scrollX: true,
                // scrollCollapse: true,
                // paging:         false,
                // fixedColumns: {
                //     left: 3,
                // }
            });
        });
    </script>
@endpush
