@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <h1 class="text-2xl font-bold">Edit Transaksi</h1>

    <x-card class="max-w-xl">
        <form action="{{ route('transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')
            @include('transactions._form')
        </form>
    </x-card>
@endsection
