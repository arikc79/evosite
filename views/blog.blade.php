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
		<li><a href="#" class="disabled button large previous">@lang('pagination_prev')</a></li>
		<li><a href="#" class="button large next">@lang('pagination_next')</a></li>
	</ul>
@endsection

@section('aside')
	@include('partials.sidebar')
@endsection
