@extends('layout')

@section('bodyClass', 'is-preload')

@section('content')
	<article class="post">
		<header>
			<div class="title">
				<h2>{{ $content['pagetitle'] }}</h2>
			</div>
		</header>
		{!! $content['content'] !!}
	</article>
@endsection

@section('aside')
	@include('partials.footer')
@endsection
