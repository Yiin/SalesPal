Removed


1. ******************************************************

list.blade.php

37

	{!! DropdownButton::normal(trans('texts.archive'))
			->withContents($datatable->bulkActions())
			->withAttributes(['class'=>'archive'])
			->split() !!}
			
archive function 

2. ******************************************************