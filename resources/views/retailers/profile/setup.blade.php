@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Setup Your Distributor Profile</h1>
    <form action="{{ route('distributors.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="company_name">Company Name</label>
            <input type="text" class="form-control" id="company_name" name="company_name" required>
        </div>

        <div class="form-group">
            <label for="company_email">Company Email</label>
            <input type="email" class="form-control" id="company_email" name="company_email" required>
        </div>

        <div class="form-group">
            <label for="company_address">Company Address</label>
            <input type="text" class="form-control" id="company_address" name="company_address" required>
        </div>

        <div class="form-group">
            <label for="company_phone_number">Company Phone Number</label>
            <input type="text" class="form-control" id="company_phone_number" name="company_phone_number" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
</div>
@endsection
