{{-- resources/views/components/layout.blade.php --}}
@props([
'title' => null,
'bodyClass' => '',
'useAdminHeader' => null,
'mainClass' => null,
])

@extends('layouts.app', [
'title' => $title,
'bodyClass' => $bodyClass,
'useAdminHeader' => $useAdminHeader,
'mainClass' => $mainClass,
])

@section('content')
{{ $slot }}
@endsection