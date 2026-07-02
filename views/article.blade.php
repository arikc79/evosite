@extends('layout')

@section('bodyClass', 'single is-preload')

@section('content')
	<article class="post">
		<header>
			<div class="title">
				<h2>{{ $content['pagetitle'] }}</h2>
				<p>{{ $content['description'] }}</p>
			</div>
			<div class="meta">
				<time class="published">{{ date('d.m.Y', $content['createdon']) }}</time>
			</div>
		</header>
		{!! $content['content'] !!}
	</article>
@endsection

@section('aside')
	@include('partials.footer')
@endsection