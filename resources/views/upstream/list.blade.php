@extends('layouts.app')

@section('content')
<script>
function searchFunction() {
    // Declare variables
    var filter, table, tr, td, i, txtValue;
    filter = document.getElementById("upstreamSearch").value.toLowerCase();
    table = document.getElementById("upstreamTable");
    tr = table.getElementsByTagName("tr");

    var visibleRowCount = 0;
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toLowerCase().startsWith(filter)) {
                tr[i].classList.remove("d-none");
                visibleRowCount++;
            } else {
                tr[i].classList.add("d-none");
            }
        }

        var noProxyFoundRow = document.getElementById("noupstreamfound");
        if (visibleRowCount === 0) {
            noProxyFoundRow.classList.remove("d-none");
        } else {
            noProxyFoundRow.classList.add("d-none");
        }
    }
}
</script>
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <a href="{{ route('proxy.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                &#129128;&#xfe0e; Back
            </a>
            Upstreams for {{ $rustProxy->server_name }}
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="col-md-3">
                    <input class="form-control me-2" type="search" placeholder="Search.." aria-label="Search" id="upstreamSearch" onkeyup="searchFunction()">
                </div>
                <button type="button" class="btn btn-primary me-sm-3" data-bs-toggle="modal" data-bs-target="#addUpstreamModal">&#x2795;&#xfe0e; Add Upstream</button>
            </div>
            <table class="table table-hover"  id="upstreamTable">
                <caption>List of upstreams</caption>
                <thead>
                    <tr>
                        <th scope="col" class="col-7">Location</th>
                        <th scope="col" class="col-1">TLS</th>
                        <th scope="col" class="col-2">Loadbalance Type</th>
                        <th scope="col" class="col-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($upstreams))
                    @foreach($upstreams as $upstream)
                    <tr>
                        <td>{{ $upstream->locations->first()->location }}
                            @if($upstream->locations->count() > 1)
                                <span class="text-muted text-nowrap">+{{ $upstream->locations->count() - 1 }} more</span>
                            @endif
                        </td>
                        <td><button type="button" class="btn btn-{{ $upstream->tls ? 'success' : 'danger' }} btn-sm" disabled>@if($upstream->tls)&#x2714;&#xfe0e;@else&#x2716;&#xfe0e;@endif</button></td>
                        <td>{{ $upstream->loadbalanceType->name }}</td>
                        <td>
                            <div class="d-flex flex-column flex-md-row">
                                <form action="{{ route('upstream.edit', $upstream) }}" method="POST" class="me-md-2 mb-2 mb-md-0">
                                @csrf
                                @method('GET')

                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>
                                <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUpstreamModal" data-bs-firstlocation="{{ $upstream->locations->first()->location }}" data-bs-url="{{ route('upstream.destroy', $upstream) }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
                    <tr id="noupstreamfound" name="noupstreamfound" class="{{ empty($upstreams) ? '' : 'd-none' }}">
                        <td class="px-4 py-2 border text-danger" colspan="4">No upstreams found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Upstream Modal -->
<div class="modal fade" id="addUpstreamModal" tabindex="-1" aria-labelledby="addUpstreamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('upstream.store', $rustProxy) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUpstreamModalLabel">Add Upstream</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Locations</label>
                        <div id="locations-container">
                            <div class="input-group mb-2 location-input">
                                <input type="text" class="form-control" name="locations[]" placeholder="Location URL">
                                <button type="button" class="btn btn-danger btn-remove-location ms-2">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" id="add-location">Add Location</button>
                    </div>
                    <div class="mb-3">
                        <label for="loadbalance_type_id" class="form-label">Loadbalance Type</label>
                        <select class="form-control @error('loadbalance_type_id') is-invalid @enderror" id="loadbalance_type_id" name="loadbalance_type_id" required>
                            @foreach($loadbalanceTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('loadbalance_type_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tls" class="form-label">TLS</label>
                        <select class="form-control @error('tls') is-invalid @enderror" id="tls" name="tls" required>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                        @error('tls')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Path Replacement</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control @error('path') is-invalid @enderror" name="path" value="{{ old('path') }}" placeholder="Path">
                            <span class="input-group-text">&#10132;</span>
                            <input type="text" class="form-control @error('replace_path') is-invalid @enderror" name="replace_path" value="{{ old('replace_path') }}" placeholder="Replace Path">
                            @if ($errors->has('path') || $errors->has('replace_path'))
                                <div class="invalid-feedback">
                                    Both Path and Replace Path must be provided.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Upstream</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUpstreamModal" name="deleteUpstreamModal" tabindex="-1" aria-labelledby="deleteUpstreamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUpstreamModalLabel">Delete Upstream</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this upstream?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')

                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
const deleteUpstreamModal = document.getElementById('deleteUpstreamModal')
if (deleteUpstreamModal) {
    deleteUpstreamModal.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget
        // Extract info from data-bs-* attributes
        const firstLocation = button.getAttribute('data-bs-firstlocation')
        const url = button.getAttribute('data-bs-url')

        // Update the modal's content.
        const modalTitle = deleteUpstreamModal.querySelector('.modal-title')
        const modalBodyInput = deleteUpstreamModal.querySelector('.modal-footer form')

        modalTitle.textContent = `Delete Upstream ${firstLocation}?`
        modalBodyInput.action = url
  })
}

document.getElementById('add-location').addEventListener('click', function () {
    const container = document.getElementById('locations-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 location-input';
    div.innerHTML = `
        <input type="text" class="form-control" name="locations[]" placeholder="Location URL">
        <button type="button" class="btn btn-danger btn-remove-location ms-2">Remove</button>
    `;
    container.appendChild(div);
});

document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('btn-remove-location')) {
        e.target.closest('.location-input').remove();
    }
});
</script>
@endsection