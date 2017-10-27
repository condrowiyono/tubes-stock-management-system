@extends('layouts.app')

@section('content')

<div class="container-fluid">
<ol class="breadcrumb">
    <li><a href="/admin">Admin</a></li>
    <li><a href="/admin/merek">merek</a> </li>
    <li class="active">{{$data->nama}} </li>
</ol>
<a title='Return'  href="{{route('merek.index')}}" ><i class='fa fa-chevron-circle-left '></i> &nbsp; Kembali ke Daftar Merek</a>

    <div class="row">
        <div class="col-md-12">
             <div class="panel panel-default">
                <div class="panel-heading">Brand : {{$data->nama}}</div>

                <div class="panel-body">
                    <table width="100%">
                        <tr>
                            <td width="40%"><strong>Nama</strong></td>
                            <td>{{$data->nama}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection