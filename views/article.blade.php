@extends('layout')

@section('bodyClass', 'single is-preload')

@section('content')
	@include('partials.breadcrumbs')
	<article class="post">
		<header>
			<div class="title">
				<h2>{{ $content['pagetitle'] }}</h2>
				<p>{{ $content['description'] }}</p>
			</div>
			<div class="meta">
				<time class="published">{{ \EvolutionCMS\Main\Helper::formatDate($content['createdon']) }}</time>
			</div>
		</header>
		{!! $content['content'] !!}

		@if(!empty($content['author_avatar']) || !empty($content['author_about']))
			<div class="author-box">
				@if(!empty($content['author_avatar']))
					<img class="author-box__avatar" src="{{ EVO_BASE_URL . $content['author_avatar'] }}" alt="">
				@endif
				@if(!empty($content['author_about']))
					<div class="author-box__about">
						<h3 class="author-box__title">@lang('about_author')</h3>
						<p>{{ $content['author_about'] }}</p>
					</div>
				@endif
			</div>
		@endif
	</article>
@endsection

@section('aside')
	@include('partials.footer')
@endsection