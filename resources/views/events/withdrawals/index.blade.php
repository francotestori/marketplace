@extends('layouts.insite-layout')

@section('custom-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-title">
            <h1>{{Lang::get('titles.wallet.withdrawals')}}</h1>
        </div>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('errors'))
            <div class="alert alert-danger" role="alert">
                {{ session('errors') }}
            </div>
        @endif

        <br>

        <div class="panel-heading table-panel">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table" id="addspaces-table" @if(count($withdrawals)) data-ride="datatables" @endif>
                            <thead>
                            <tr>
                                <th>{{Lang::get('tables.wallet.id')}}</th>
                                <th>{{Lang::get('tables.wallet.requester')}}</th>
                                <th>{{Lang::get('tables.wallet.state')}}</th>
                                <th>{{Lang::get('tables.wallet.amount')}}</th>
                                <th>{{Lang::get('tables.wallet.authorize')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($withdrawals))
                                @foreach($withdrawals as $withdrawal)
                                    <tr>
                                        <td>{{$withdrawal->id}}</td>
                                        <td>{{$withdrawal->getSender()->getUser()->email}}</td>
                                        <td>
                                            <span class="btn btn-block btn-table-border
                                                                       @if($withdrawal->getEvent()->pending()) btn-warning
                                                                       @elseif($withdrawal->getEvent()->accepted()) btn-success
                                                                       @else btn-danger
                                                                       @endif" disabled>
                                                {{Lang::get('tables.'.$withdrawal->getEvent()->state)}}
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                <strong>{{Lang::get('attributes.currency')}}</strong>
                                                {{$withdrawal->amount}}
                                            </span>
                                        </td>
                                        <td>
                                            @if($withdrawal->getEvent()->pending())
                                                <button class="btn btn-warning" data-toggle="modal" data-target="{{'#withdraw-'.$withdrawal->id}}">
                                                    {{Lang::get('forms.withdrawals.authorize')}}
                                                </button>
                                                @include('events.withdrawals.withdraw', ['$withdrawal' => $withdrawal])
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">{{Lang::get('messages.no_items_found')}}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
@stop

@section('custom-js')
    <!-- DataTables -->
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <script>
        // datatable
        $('[data-ride="datatables"]').each(function() {
            var oTable = $(this).dataTable(
                {
                    "language": {
                        "paginate": {
                            "previous": "{{Lang::get('tables.previous')}}",
                            "next": "{{Lang::get('tables.next')}}",
                            "first": "{{Lang::get('tables.first')}}",
                            "last": "{{Lang::get('tables.last')}}"
                        },
                        "emptyTables": "{{Lang::get('tables.empty')}}",
                        "lengthMenu": "{{Lang::get('tables.lengthMenu')}}",
                        "info": "{{Lang::get('tables.info')}}",
                        "search": "{{Lang::get('tables.search')}}"
                    }
                }
            );
        });
    </script>
@endsection
