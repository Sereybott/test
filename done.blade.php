@extends('layout')

@section('content')
<nav class="navbar navbar-expand-sm bg-white navbar-white border fixed-top">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <a class="navbar-brand" href="{{route('home')}}">
                <img src="{{URL::asset('/nara-k_logo.jpg')}}" alt="Avatar Logo" style="width:40px;" class=""> 
            </a>
            <ul class="navbar-nav me-auto" >
                <li class="nav-item">
                    <a href="{{route('home')}}" class="nav-link" onclick="kadai()">課題</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('completed')}}" class="nav-link active">完成</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('overdue')}}" class="nav-link">期限を経過</a>
                </li>
                @if(Auth::user()->is_admin)
                <li class="nav-item">
                    <a href="{{route('student')}}" class="nav-link">学生の情報</a>
                </li>
                @endif
            </ul>
            <ul class="d-flex navbar-nav">
                <li class="dropdown nav-item">
                    <button class="nav-link dropdown-toggle" id="dropdownMenuButton2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php use App\Models\kurasu;?>
                        {{Auth::user()->name}}-{{kurasu::where('id',Auth::user()->class)->first()['name']}}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{route('setting')}}">設定</a></li>
                        @if(Auth::user()->is_admin )
                            <li><a class="dropdown-item" href="{{route('addhomework')}}">課題の追加</a></li>
                            <li><a class="dropdown-item" href="{{route('addstudent')}}">学生の追加</a></li>
                        @endif
                        <li>
                            <form action="{{route('logout')}}" method="POST" role="search" class="dropdown-item">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item p-0">ログアウト</button >
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </div>
</nav>
<div class="row mx-5 row-cols-1 row-cols-md-2 row-cols-lg-3" style="margin-top:100px">
    @foreach($homeworks as $homework)
        <?php 
            $date1=date_create(date("Y-m-d"));
            $date2=date_create($homework['deadline']);
            $diff=date_diff($date1,$date2);
            $done_hw = DB::table('dones')->where('homework_id',$homework->id)->get();
            $done = $done_hw->count();
            $donedate = DB::table('dones')->where('homework_id',$homework->id)->where('user_id',Auth::user()->id)->get();
            $isDone = $donedate->count();
        ?>
        @if($isDone)
        <div class="col px-5 my-2">
            <div class="card shadow mb-5 p-0">
                @if(strtotime($donedate[0]->date_submit)<=strtotime($homework['deadline']))
                <div class="card-header bg-success text-white text-center py-3"><h3 class="my-0">{{$homework['deadline']}}</h3></div>
                @else
                <div class="card-header bg-warning text-light text-center py-3"><h3 class="my-0">{{$homework['deadline']}}</h3></div>
                @endif
                <div class="card-body px-4 py-4">
                    
                    <p class="card-text fw-bold">{{$homework['subject']}}</p>
                    <p class="card-text">{{$homework['description']}}</p>
                    <p class="card-text">{{$donedate[0]->date_submit}}に終わった
                        @if(strtotime($donedate[0]->date_submit)>strtotime($homework['deadline']))
                        （遅い）
                        @endif
                    </p>
                    
                    <div class="progress my-4">
                        @if(strtotime($donedate[0]->date_submit)<=strtotime($homework['deadline']))
                        <div class="progress-bar bg-success" style={{"width:".($done*100/$student_num)."%"}}></div>
                        @else
                        <div class="progress-bar bg-warning" style={{"width:".($done*100/$student_num)."%"}}></div>
                        @endif
                    </div>
                    <div style="display:block; text-align:center;">
                        <form action="{{route('restore')}}" method="POST" role="search">
                            @csrf
                            @method('delete')
                            <input type="hidden" name="homework_id" value="{{$homework->id}}">
                            @if(strtotime($donedate[0]->date_submit)<=strtotime($homework['deadline']))
                            <button class="btn btn-success px-4" onclick="showConfirmation()" type="submit">差し戻し</button>
                            @else
                            <button class="btn btn-warning text-light px-4" onclick="showConfirmation()" type="submit">差し戻し</button>
                            @endif
                        </form>
                    </div>
                </div>
                <script>
                    function showConfirmation() {
                      if (!confirm("差し戻し？？")) {
                        event.preventDefault();
                      } 
                    }
                  </script>
            </div>
        </div>
        @endif
    @endforeach
</div>
    
@endsection