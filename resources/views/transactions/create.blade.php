@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <h1 class="text-2xl font-bold">Tambah Transaksi</h1>

    <x-card class="max-w-xl">
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            @include('transactions._form')
        </form>
    </x-card>
@endsection
