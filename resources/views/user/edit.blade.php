@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <form method="post" action="{{ route('user.update', $user->id )}}">
                        @csrf
                        <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Name</label>
                          <input type="hidden" name="id" value="{{ $user->id }}">
                          <input type="text" class="form-control" id="exampleInputEmail1" name="name" value="{{$user->name}}" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Email address</label>
                          <input type="email" class="form-control" value="{{$user->email}}" name="email" id="exampleInputEmail1" aria-describedby="emailHelp">
                          <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">QR CODE</label>
                            {!! $svg_code !!}

                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
