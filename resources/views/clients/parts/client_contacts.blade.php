@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .flatpickr-wrapper{
            width:100%;
        }
        .form-file-control {
            display: block;
            width: 100%;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            font-size: .813rem;
            line-height: 1.5;
            color: #665c70;
            background-color: #fff;
            background-clip: padding-box;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .action{
            padding-top: 17px;
            display: inline-flex;
        }
    </style>
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Clients</h1>
        <ul>
            <li><a href="{{ route('clients.clients.orders') }}">Clients</a></li>
            <li>Contacts</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Contacts</h6>
                    
                    <div class="separator-breadcrumb">
                            <a href="javascript:void(0);" onclick="getModal('{{ route('clients.createContact',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Contact
                            </a>
                    </div>
                    <div class="dropdown dropleft text-right w-50 float-right">
                    </div>
                </div>
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table id="contacts" class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th scope="col" id="idclass">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Office Phone</th>
                                        <th scope="col">Cell Phone</th>
                                        <th scope="col">Contact Notes</th>
                                        <th scope="col">Cranium</th>
                                        <th scope="col">Primary</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <!-- DATATABLE Here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true"></div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>
       $(document).ready(function () {
            contactList();
       });
    


    function contactList(){
        $('#contacts').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('clients.datatable.clientContactList',$row->id) }}',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
				{data: 'title', name: 'title'},
                {data: 'email', name: 'email'},
                {data: 'office_phone', name: 'office_phone'},
				{data: 'cell_phone', name: 'cell_phone'},
                {data: 'contact_note', name: 'contact_note'},
                {data: 'cranium', name: 'cranium'},
                {data: 'status', name: 'status',searchable: false},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false,
                }
            ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }



    function getModal(url){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#exampleModal").html('');
                $('#exampleModal').html(response);
                $('#exampleModal').modal('show');
            }
        })
    }

    function editModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#editModal").html('');
                $('#editModal').html(response);
                $('#editModal').modal('show');
            }
        })
    }


    

    function deleteContact(url){
        if(confirm('Are You Sure You Want To Delete This?')){
            $.ajax({
                type:'GET',
                url:url,
                success:function(response){
                    toastr.success("Success")
                    contactList()
                }
            })
        }
    }



   </script>
@endsection
