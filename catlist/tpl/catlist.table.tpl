<!-- BEGIN: MAIN -->
	<div class="wrapper">
		<table class="table table-striped">
			<tbody>
<!-- BEGIN: PAGE_ROW -->
				<tr>
					<td>
						<a href="{PAGE_ROW_CODE|cot_url('page', 'c=$this')}">{PAGE_ROW_TITLE}</a>
					</td>
					<td class="text-right">
						{PAGE_ROW_HITS|cot_declension($this, 'Hits')}
					</td>
				</tr>
<!-- END: PAGE_ROW -->
			</tbody>
		</table>
	</div>
<!-- END: MAIN -->
