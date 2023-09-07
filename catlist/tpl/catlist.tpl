<!-- BEGIN: MAIN -->
<ul class="list-unstyled">
<!-- BEGIN: PAGE_ROW -->
	<li class="{PAGE_ROW_ODDEVEN}">
		<a href="{PAGE_ROW_CODE|cot_url('page', 'c=$this')}">{PAGE_ROW_TITLE}</a>
	</li>
<!-- END: PAGE_ROW -->
</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
<nav aria-label="Catlist Pagination">
	<ul class="pagination pagination-sm justify-content-center mb-0">
		{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
	</ul>
</nav>
<!-- END: MAIN -->
