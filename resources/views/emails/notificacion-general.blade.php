@extends('emails.layouts.peec')

@section('titulo', $titulo)

@section('content')
    <p style="margin:0 0 14px;">Estimado laboratorio <strong>{{ $nombreLab }}</strong>,</p>

    <div style="font-size:14px;line-height:21px;color:#333333;">
        {!! \App\Services\NotificacionLabService::sanitizar($html) !!}
    </div>
@endsection
