@section('content')

<div class="row">
    <div class="col-lg-12">
        <h2 class="text-center">News Application</h2>
    </div>
</div>

@if(sizeof($results) > 0)
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>News Source</th>
            <!-- <th>Section</th>
            <th>Link</th>
            <th>Publication Date</th> -->
        </tr>
        {{$i = 0;}}
        @foreach ($results as $result)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $result->webTitle }}</td>
                <td>{{ $result->webUrl }}</td>
                <td>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-alert">No Result found</div>
@endif