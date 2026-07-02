@extends('layout')

@section('bodyClass', 'is-preload')

@section('content')
	@foreach($articles as $doc)
		@include('partials.article-item', ['item' => [
			'pagetitle' => $doc->pagetitle,
			'description' => $doc->description,
			'introtext' => $doc->introtext,
			'link' => $doc->fullLink,
			'createdon' => date('d.m.Y', $doc->createdon_orig),
			'thumb' => \EvolutionCMS\Main\Helper::articleThumb($doc->id),
		]])
	@endforeach
	<ul class="actions pagination">
		<li>
			@if ($articles->onFirstPage())
				<a href="#" class="disabled button large previous">@lang('pagination_prev')</a>
			@else
				<a href="{{ $articles->previousPageUrl() }}" class="button large previous">@lang('pagination_prev')</a>
			@endif
		</li>
		<li>
			@if ($articles->hasMorePages())
				<a href="{{ $articles->nextPageUrl() }}" class="button large next">@lang('pagination_next')</a>
			@else
				<a href="#" class="disabled button large next">@lang('pagination_next')</a>
			@endif
		</li>
	</ul>
@endsection

@section('aside')
	@include('partials.sidebar')
@endsection
