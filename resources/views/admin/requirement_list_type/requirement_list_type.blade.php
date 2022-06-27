@extends('layouts.app')

{{-- NAVBAR --}}
@section('navbar')
    @include('layouts.admin_navbar')
@endsection

{{-- SIDEBAR --}}
@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('section_header')
    <h1 id="page_title"></h1>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <p id="deadline"></p>
@endsection

    {{-- MODAL --}}
    @include('admin/requirement_list_type/requirement_list_type_modal')

    {{-- CONTENT --}}
    @section('content')

            {{-- FORM --}}
            @include('admin/requirement_list_type/requirement_list_type_form')

            {{-- DATATABLE --}}
            @include('admin/requirement_list_type/requirement_list_type_datatable')
    @endsection



{{-- FOOTER --}}
@section('footer')
    @include('layouts.admin_footer')
@endsection


@section('script')
    @include('admin/requirement_list_type/requirement_list_type_scripts')
@endsection