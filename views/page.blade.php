@extends('layout')

@section('bodyClass', 'is-preload')

@section('content')
	@include('partials.breadcrumbs')
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
	@isset($popularArticles)
		@include('partials.popular-articles')
	@endisset
	@include('partials.footer')
@endsection
