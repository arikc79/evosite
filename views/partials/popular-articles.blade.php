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
@endif
