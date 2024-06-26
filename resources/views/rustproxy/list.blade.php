@extends('layouts.app')

@section('content')
<script>
function searchFunction() {
    // Declare variables
    var filter, table, tr, td, i, txtValue;
    filter = document.getElementById("domainSearch").value.toLowerCase();
    table = document.getElementById("domainTable");
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

        var noProxyFoundRow = document.getElementById("noproxyfound");
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
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            Proxy List
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="col-md-3">
                    <input class="form-control me-2" type="search" placeholder="Search.." aria-label="Search" id="domainSearch" onkeyup="searchFunction()">
                </div>
                <button type="button" class="btn btn-primary me-sm-3" data-bs-toggle="modal" data-bs-target="#addProxyModal">&#x2795;&#xfe0e; Add Proxy</button>
            </div>
            <table class="table table-hover" id="domainTable">
                <caption>List of proxies</caption>
                <thead>
                    <tr>
                        <th scope="col" class="col-7">Domain Name</th>
                        <th scope="col" class="col-2">SSL</th>
                        <th scope="col" class="col-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($proxies))
                    @foreach($proxies as $proxy)
                    <tr>
                        <td>{{ $proxy->server_name }}
                        @if($proxy->id == $defaultApp)
                            <span class="badge rounded-pill text-bg-success">default</span>
                        @endif
                        @if($proxy->upstreams->isEmpty())
                            <span class="badge rounded-pill text-bg-warning">No Upstreams</span>
                        @endif</td>
                        <td><button type="button" class="btn btn-{{ $proxy->ssl_enabled ? 'success' : 'danger' }} btn-sm" disabled>@if($proxy->ssl_enabled)&#x2714;&#xfe0e;@else&#x2716;&#xfe0e;@endif</button></td>
                        <td>
                            <a href="{{ route('upstream.list', $proxy) }}" class="text-decoration-none d-block d-sm-inline-block text-nowrap me-sm-2">&#x1F441; Upstreams</a>
                            <a href="{{ route('proxy.edit', $proxy) }}" class="text-decoration-none d-block d-sm-inline-block text-nowrap me-sm-2">&#x270E; Edit</a>
                            <a href="#" class="text-decoration-none d-block d-sm-inline-block text-nowrap" data-bs-toggle="modal" data-bs-target="#deleteProxyModal" data-bs-servername="{{ $proxy->server_name }}" data-bs-url="{{ route('proxy.destroy', $proxy) }}">&#x2718; Delete</a>
                        </td>
                    </tr>
                    @endforeach
                @endif
                    <tr id="noproxyfound" name="noproxyfound" class="{{ empty($proxies) ? '' : 'd-none' }}">
                      <td class="px-4 py-2 border text-danger" colspan="3">No proxies found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Proxy Modal -->
<div class="modal fade" id="addProxyModal" tabindex="-1" aria-labelledby="addProxyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProxyModalLabel">Add New Proxy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('proxy.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="app_name" class="form-label">App Name</label>
            <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name') }}" required>
            @error('app_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="server_name" class="form-label">Server Name</label>
            <input type="text" class="form-control @error('server_name') is-invalid @enderror" id="server_name" name="server_name" value="{{ old('server_name') }}" required>
            @error('server_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
          <div class="form-check mb-3">
            <input type="hidden" name="ssl_enabled" value="0">
            <input class="form-check-input" type="checkbox" id="ssl_enabled" name="ssl_enabled" value="1" {{ old('ssl_enabled') ? 'checked' : '' }}>
            <label class="form-check-label" for="ssl_enabled">
              SSL Enabled
            </label>
          </div>
          <div class="form-check mb-3">
            <input type="hidden" name="https_redirection" value="0">
            <input class="form-check-input" type="checkbox" id="https_redirection" name="https_redirection" value="1" {{ old('https_redirection') ? 'checked' : '' }}>
            <label class="form-check-label" for="https_redirection">
              HTTPS Redirection
            </label>
          </div>
          <div class="mb-3">
            <label for="tls_cert_path" class="form-label">TLS Cert Path</label>
            <input type="text" class="form-control @error('tls_cert_path') is-invalid @enderror" id="tls_cert_path" name="tls_cert_path" value="{{ old('tls_cert_path') }}">
            @error('tls_cert_path')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="tls_cert_key_path" class="form-label">TLS Cert Key Path</label>
            <input type="text" class="form-control @error('tls_cert_key_path') is-invalid @enderror" id="tls_cert_key_path" name="tls_cert_key_path" value="{{ old('tls_cert_key_path') }}">
            @error('tls_cert_key_path')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="client_ca_cert_path" class="form-label">Client CA Cert Path</label>
            <input type="text" class="form-control @error('client_ca_cert_path') is-invalid @enderror" id="client_ca_cert_path" name="client_ca_cert_path" value="{{ old('client_ca_cert_path') }}">
            @error('client_ca_cert_path')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProxyModal" name="deleteProxyModal" tabindex="-1" aria-labelledby="deleteProxyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProxyModalLabel">Delete Proxy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this proxy?
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
const deleteProxyModal = document.getElementById('deleteProxyModal')
if (deleteProxyModal) {
    deleteProxyModal.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget
        // Extract info from data-bs-* attributes
        const servername = button.getAttribute('data-bs-servername')
        const url = button.getAttribute('data-bs-url')

        // Update the modal's content.
        const modalTitle = deleteProxyModal.querySelector('.modal-title')
        const modalBodyInput = deleteProxyModal.querySelector('.modal-footer form')

        modalTitle.textContent = `Delete Proxy ${servername}?`
        modalBodyInput.action = url
  })
}
</script>
@if($errors->has('app_name') || $errors->has('server_name') || $errors->has('ssl_enabled') || $errors->has('https_redirection') || $errors->has('tls_cert_path') || $errors->has('tls_cert_key_path') || $errors->has('client_ca_cert_path'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var addProxyModal = new Modal(document.getElementById('addProxyModal'), {
        backdrop: 'static'
    });
    addProxyModal.show();
});
</script>
@endif
@endsection
