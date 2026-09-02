<div class="col-sm-12 user_name">
    <label class="form-label">Nama: </label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="user_name" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="user2" required />
        <input type="hidden" name="user_id">
    </div>
    {{-- <select class="select2 form-select" name="user_id">
        @foreach ($user as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select> --}}
</div>
<div class="col-sm-12">
    <label class="form-label">Device</label>
    <select class="select2 form-select" name="device_id">
        @foreach ($device as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-sm-12">
    <label class="form-label">Verify</label>
    <select class="select2 form-select" name="verify_id">
        @foreach ($verify as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-sm-12">
    <label class="form-label" for="name">Tanggal</label>
    <div class="input-group input-group-merge">
        <input type="date" class="form-control" name="tgl" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="name2" required />
    </div>
</div>
<div class="col-sm-12">
    <label class="form-label" for="name">Jam Datang</label>
    <div class="input-group input-group-merge">
        <input type="time" class="form-control" name="jam" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="name2" required />
    </div>
</div>
<div class="col-sm-12">
    <label class="form-label" for="name">Jam Pulang</label>
    <div class="input-group input-group-merge">
        <input type="time" class="form-control" name="jam_pulang" placeholder="Type here..." aria-label="Type here..."
            aria-describedby="name2" required />
    </div>
</div>
{{-- <div class="">
    <label class="form-label">Latitude</label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="latitude" placeholder="Type here.." aria-label="Type here.." />

    </div>
</div>
<div class="">
    <label class="form-label">Longitude</label>
    <div class="input-group input-group-merge">
        <input type="text" class="form-control" name="longitude" placeholder="Type here.."
            aria-label="Type here.." />

    </div>
</div> --}}
<div class="">
    <label class="form-label">Keterangan</label>
    <div class="input-group input-group-merge">
        <textarea name="ket" id="ket" class="form-control" cols="20" rows="10" placeholder="Type here.."></textarea>

    </div>
</div>
