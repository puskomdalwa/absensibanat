@extends('layouts.admin.template')
@section('title', 'Assign Users - ' . $type->nama)
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Assign Users to Type: <span class="text-primary">{{ $type->nama }}</span></h4>
        <a href="{{ route('admin.type.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Select the users you want to assign to <strong>{{ $type->nama }}</strong>. Unchecked users will have their type set to <em>null</em>. Users already assigned to other types cannot be checked.</p>
            
            <div class="row align-items-center mb-4">
                <div class="col-md-4">
                    <label class="form-label" for="filter-departemen">Filter Departemen</label>
                    <select id="filter-departemen" class="form-select">
                        <option value="">Semua Departemen</option>
                        @foreach($departemen as $dept)
                            <option value="{{ $dept->nama }}">{{ $dept->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-md-end mt-3 mt-md-0">
                    <span class="fs-5 fw-semibold">
                        Total Dicentang: <span id="selected-count" class="badge bg-primary fs-6">0</span>
                    </span>
                </div>
            </div>

            <form id="form-assign" action="{{ route('admin.type.assign.store', $type->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover" id="table-assign">
                        <thead>
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="check-all" class="form-check-input">
                                </th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Departemen</th>
                                <th>Current Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $isAlreadyAssignedOtherType = $user->type_id && $user->type_id != $type->id;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $user->id }}" class="form-check-input user-checkbox"
                                            {{ $user->type_id == $type->id ? 'checked' : '' }}
                                            {{ $isAlreadyAssignedOtherType ? 'disabled' : '' }}>
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ optional($user->role)->akses }}</td>
                                    <td>{{ optional($user->departemen)->nama }}</td>
                                    <td>
                                        @if($user->type_id == $type->id)
                                            <span class="badge bg-label-success">{{ $type->nama }}</span>
                                        @elseif($user->type)
                                            <span class="badge bg-label-secondary" data-bs-toggle="tooltip" title="Already assigned to other type">{{ $user->type->nama }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                    <a href="{{ route('admin.type.index') }}" class="btn btn-label-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#table-assign').DataTable({
                order: [[1, 'asc']], // order by name
                pageLength: 50,
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false }
                ]
            });

            // Calculate selected count
            function updateSelectedCount() {
                var count = 0;
                table.$('input[type="checkbox"].user-checkbox').each(function() {
                    if (this.checked) {
                        count++;
                    }
                });
                $('#selected-count').text(count);
            }

            // Initial call
            updateSelectedCount();

            // Filter departemen
            $('#filter-departemen').on('change', function() {
                table.column(4).search($(this).val()).draw();
            });

            // Check all checkbox (only check non-disabled ones)
            $('#check-all').on('click', function() {
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"].user-checkbox:not(:disabled)', rows).prop('checked', this.checked);
                updateSelectedCount();
            });

            // If a checkbox is clicked, check check-all state & update count
            $('#table-assign tbody').on('change', 'input[type="checkbox"].user-checkbox', function() {
                updateSelectedCount();
                var el = $('#check-all').get(0);
                if (el && el.checked && ('checked' in this) && !this.checked) {
                    el.checked = false;
                }
            });

            // Form submit: include checkboxes from all pages
            $('#form-assign').on('submit', function(e) {
                var form = this;
                
                // Disable the submit button to prevent double clicks
                var submitBtn = $(form).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');

                // Clear any previously appended hidden inputs to avoid duplicates if submitted multiple times
                $(form).find('input[name="user_ids[]"]').remove();

                // Iterate over all checkboxes in the table (including not in current DOM page)
                table.$('input[type="checkbox"].user-checkbox').each(function() {
                    if (this.checked && !this.disabled) {
                        $(form).append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', 'user_ids[]')
                                .val(this.value)
                        );
                    }
                });
            });
        });
    </script>
@endpush
