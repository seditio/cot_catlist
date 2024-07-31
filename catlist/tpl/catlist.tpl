<!-- BEGIN: MAIN -->
<table class="table table-striped mb-3">
	<tbody>
<!-- BEGIN: PAGE_ROW -->
		<tr class="{PAGE_ROW_ODDEVEN}">
			<td>
				<!-- IF {PAGE_ROW_HAS_DOT} -->
				<a href="{PAGE_ROW_URL}" class="ms-2">&#x2022;<span class="ms-2">{PAGE_ROW_TITLE}</span></a>
				<!-- ELSE -->
				<a href="{PAGE_ROW_URL}" class="fw-bold">{PAGE_ROW_TITLE}</a>
				<!-- ENDIF -->
			</td>
			<td class="d-none d-sm-table-cell">
				{PAGE_ROW_AREA}
			</td>
			<td class="text-end">
				<!-- IF {PAGE_ROW_COUNT} -->{PAGE_ROW_COUNT|cot_declension($this, 'Entries')}<!-- ENDIF -->
			</td>
		</tr>
<!-- END: PAGE_ROW -->
	</tbody>
</table>

<!-- IF {PAGINATION} -->
<nav aria-label="Catlist Pagination">
	<ul class="pagination pagination-sm justify-content-center mb-0">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
	</ul>
</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
