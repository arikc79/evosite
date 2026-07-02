@if($popularArticles->isNotEmpty())
	<section class="blurb">
		<h2>@lang('sidebar_popular_title')</h2>
		<ul class="popular-articles">
			@foreach($popularArticles as $article)
				<li>
					{!! \EvolutionCMS\Main\Helper::articleThumb($article->id) !!}
					<a href="{{ $article->fullLink }}">{{ $article->pagetitle }}</a>
					@if($article->hits > 0)
						<span class="popular-articles__hits">{{ $article->hits }} @lang('sidebar_popular_views')</span>
					@endif
				</li>
			@endforeach
		</ul>
	</section>
	<style>
		.popular-articles { list-style: none; margin: 0; padding: 0; }
		.popular-articles li { margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid rgba(0,0,0,0.1); }
		.popular-articles li:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
		.popular-articles img { max-width: 100%; height: auto; margin-bottom: 0.5em; }
		.popular-articles__hits { display: block; font-size: 0.8em; opacity: 0.6; }
	</style>
@endif
