<div class="col-sm-12">
    <label class="form-label" for="username">Username</label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="username" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="username2" required />
    </div>
</div>
<div class="col-sm-12">
    <label class="form-label" for="name">Name</label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="name" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="name2" required />
    </div>
</div>
<div class="">
    <label class="form-label">Email</label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="email" placeholder="Type here.." aria-label="Type here.."
            aria-describedby="email" />
        <span class="input-group-text">@example.com</span>
    </div>
</div>
@if (\Auth::user()->isAdmin())
    <div class="">
        <label class="form-label">Role</label>
        <select class="select2 form-select" name="role_id">
            @foreach ($role as $item)
                <option value="{{ $item->id }}">{{ $item->akses }}</option>
            @endforeach
        </select>
    </div>
@else
    <input type="hidden" name="role_id" value="{{ optional($role->first())->id }}">
    <div class="">
        <label class="form-label">Role</label>
        <input type="text" class="form-control" value="user" disabled>
    </div>
@endif
<div class="">
    <label class="form-label">Departemen</label>
    <select class="select2 form-select" name="departemen_id">
        @foreach ($departemen ?? [] as $item)
            <option value="{{ $item->id }}">{{ $item->nama }}</option>
        @endforeach
    </select>
</div>
<div class="">
    <label class="form-label">Type User</label>
    <select class="select2 form-select" name="type_id">
        <option value="">-- No Type --</option>
        @foreach ($type ?? [] as $item)
            <option value="{{ $item->id }}">{{ $item->nama }}</option>
        @endforeach
    </select>
</div>
<div class="">
    <label class="form-label">Gender</label>
    <select class="select2 form-select" name="jenis_kelamin">
        @foreach (\Helper::getEnumValues('users', 'jenis_kelamin', ['*']) as $item)
            <option>{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="">
    <label class="form-label">Photo</label>
    <div class="d-flex align-items-end mb-3">
        <div class="preview d-flex align-items-center position-relative">
            <img class="cropped-image" alt="Cropped Preview"
                src="{{ asset('admin_assets/img/avatars/profile.png') }}" />
        </div>
        <button type="button" class="btn btn-danger ms-2 remove-image d-none">
            Remove
        </button>
    </div>
    <input class="form-control" type="file" name="upload_photo" />
    <input type="hidden" name="photo">
</div>
@if ($showPassword ?? false)
    <div class="">
        <div class="form-password-toggle">
            <label class="form-label">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    @if ($passwordRequired ?? false) required @endif />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
        </div>
    </div>
    <div class="">
        <div class="form-password-toggle">
            <label class="form-label">Confirm Password</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control" name="confirm_password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    @if ($passwordRequired ?? false) required @endif />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
        </div>
    </div>
@endif
