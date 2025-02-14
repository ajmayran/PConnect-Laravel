@extends('layouts.app')
    <div class="container">
        <h1>Profile Setup</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.updateSetup') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="company_profile_image">Company Profile Image</label>
                <input type="file" class="form-control" id="company_profile_image" name="company_profile_image">
            </div>
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
                <input type="text" class="form-control" id="company_phone_number" name="company_phone_number"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Complete Setup</button>
        </form>
    </div>
