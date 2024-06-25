@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <a href="{{ route('upstream.list', $upstream->rustProxy) }}" class="btn btn-outline-secondary btn-sm me-3">
                &#129128;&#xfe0e; Back
            </a>
            Edit Upstream
        </div>
        <div class="card-body">
            <form action="{{ route('upstream.update', $upstream->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Locations</label>
                    <div id="locations-container">
                        @foreach(old('locations', $upstream->locations->map(function ($loc) { return ['location' => $loc->location, 'tls' => $loc->tls]; })->toArray()) as $index => $locationData)
                            <div class="input-group mb-2 location-input">
                                <input type="url" class="form-control @error('locations.' . $index . '.location') is-invalid @enderror" name="locations[{{ $index }}][location]" value="{{ $locationData['location'] }}" placeholder="Location URL">
                                <div class="input-group-text">
                                    <input type="hidden" name="locations[{{ $index }}][tls]" value="0">
                                    <input type="checkbox" class="form-check-input ms-2" name="locations[{{ $index }}][tls]" value="1" {{ !empty($locationData['tls']) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2">TLS</label>
                                </div>
                                <button type="button" class="btn btn-danger btn-remove-location ms-2">Remove</button>
                                @error('locations.' . $index . '.location')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary" id="add-location">Add Location</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Path Replacement</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control @error('path') is-invalid @enderror" name="path" value="{{ old('path', $upstream->path) }}" placeholder="Path">
                        <span class="input-group-text">&#10132;</span>
                        <input type="text" class="form-control @error('replace_path') is-invalid @enderror" name="replace_path" value="{{ old('replace_path', $upstream->replace_path) }}" placeholder="Replace Path">
                        @if ($errors->has('path') || $errors->has('replace_path'))
                            <div class="invalid-feedback">
                                Both Path and Replace Path must be provided.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tls" class="form-label">TLS</label>
                    <select class="form-control @error('tls') is-invalid @enderror" id="tls" name="tls" required>
                        <option value="1" {{ old('tls', $upstream->tls) ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ old('tls', $upstream->tls) ? '' : 'selected' }}>Disabled</option>
                    </select>
                    @error('tls')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="loadbalance_type_id" class="form-label">Loadbalance Type</label>
                    <select class="form-control @error('loadbalance_type_id') is-invalid @enderror" id="loadbalance_type_id" name="loadbalance_type_id" required>
                        @foreach($loadbalanceTypes as $type)
                            <option value="{{ $type->id }}" {{ old('loadbalance_type_id', $upstream->loadbalance_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('loadbalance_type_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <div>
                        @foreach($upstreamOptions as $option)
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="option_{{ $option->id }}" name="options[]" value="{{ $option->id }}" 
                                {{ in_array($option->id, old('options', $upstream->upstreamOptions->pluck('id')->toArray())) ? 'checked' : '' }}
                                @if($option->option === 'force_http2_upstream' || $option->option === 'force_http11_upstream') onclick="handleMutualExclusion(this)" @endif>
                                <label class="form-check-label" for="option_{{ $option->id }}">{{ $option->option }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('options')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
    function handleMutualExclusion(checkbox) {
        const forceHttp2Upstream = document.getElementById('option_{{ $upstreamOptions->firstWhere("option", "force_http2_upstream")->id }}');
        const forceHttp11Upstream = document.getElementById('option_{{ $upstreamOptions->firstWhere("option", "force_http11_upstream")->id }}');

        if (checkbox.id === forceHttp2Upstream.id && checkbox.checked) {
            forceHttp11Upstream.checked = false;
        }

        if (checkbox.id === forceHttp11Upstream.id && checkbox.checked) {
            forceHttp2Upstream.checked = false;
        }
    }

    document.getElementById('add-location').addEventListener('click', function () {
        const container = document.getElementById('locations-container');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'input-group mb-2 location-input';
        div.innerHTML = `
            <input type="text" class="form-control" name="locations[${index}][location]" placeholder="Location URL">
            <div class="input-group-text">
                <input type="hidden" name="locations[${index}][tls]" value="0">
                <input type="checkbox" class="form-check-input ms-2" name="locations[${index}][tls]" value="1">
                <label class="form-check-label ms-2">TLS</label>
            </div>
            <button type="button" class="btn btn-danger btn-remove-location ms-2">Remove</button>
        `;
        container.appendChild(div);
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-remove-location')) {
            e.target.closest('.input-group').remove();
        }
    });
</script>
@endsection
