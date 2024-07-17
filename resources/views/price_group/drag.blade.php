@extends('layouts.master')
@section('page-css')
 <style type="text/css">
 .squre {
  float: left;
  width: 100px;
  height: 50px;
  margin: 10px;
  padding: 10px;
  border: 1px solid black;
}
#drag4, #drag5{
  float: left;
  width: 200px;
  height: 400px;
  margin: 10px;
  padding: 10px;
  border: 1px solid black;
}
</style>
@endsection
@section('main-content')
<div ondrop="drop(event)" ondragover="allowDrop(event)" id="drag5">
	<div draggable="true" class="squre" ondragstart="drag(event)" id="drag3">
		product cost
	</div>
	<div draggable="true" class="squre" ondragstart="drag(event)" id="drag7">
		Aqustion cost
	</div>
	<div draggable="true" class="squre" ondragstart="drag(event)" id="drag8">
		shipping cost
	</div>
</div>
<div ondrop="drop(event)" ondragover="allowDrop(event)" id="drag4"></div>
@endsection

@section('page-js')
<script type="text/javascript">
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  ev.target.appendChild(document.getElementById(data));
}
</script>
@endsection