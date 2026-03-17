@extends('errors.layout')
@section('code', '403')
@section('title', 'Forbidden')
@section('message', $exception->getMessage() ?: 'You do not have permission to access this page.')
