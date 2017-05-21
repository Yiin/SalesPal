{!! Former::open(Utils::pluralizeEntityType($entityType) . '/bulk')
		->addClass('listForm_' . $entityType) !!}

<div style="display:none">
	{!! Former::text('action')->id('action_' . $entityType) !!}
    {!! Former::text('public_id')->id('public_id_' . $entityType) !!}
    {!! Former::text('datatable')->value('true') !!}
</div>

{{-- pridedama row plius col pagal dydzius --}}
<div class="row">
	<div class="col-xs-2">
		<div class="buttonPrimary">
			@if ($entityType == ENTITY_EXPENSE)
                {!! Button::normal(trans('texts.categories'))->asLinkTo(URL::to('/expense_categories'))->appendIcon(Icon::create('list')) !!}
            @elseif ($entityType == ENTITY_TASK)
                {!! Button::normal(trans('texts.projects'))->asLinkTo(URL::to('/projects'))->appendIcon(Icon::create('list')) !!}
            @endif

            @if (Auth::user()->can('create', $entityType) && empty($vendorId))
                {!! Button::primary(Icon::create('user pull-left').(mtrans($entityType, "new_{$entityType}")))->asLinkTo(url(Utils::pluralizeEntityType($entityType) . '/create/' . (isset($clientId) ? $clientId : ''))) !!}
            @endif
		</div>
	</div>
	<div class="col-xs-3">
		<div class="dropdown">
			<button class="btn btn-primary dropdown-toggle devel-dropdown-button" type="button" data-toggle="dropdown"><p class="devel-dropdown-text">Select to show</p>
				<span class="caret caret-dev-top"></span></button>
			<ul class="dropdown-menu devel-dropdown-menu">
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb2" id="select-all-chkbx" type="checkbox">
					<label for="cb2">&nbsp;All</label>
				</li>
				<hr class="separator"/>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb1" type="checkbox">
					<label for="cb1">&nbsp;Active</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb3" type="checkbox">
					<label for="cb3">&nbsp;Inactive</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb4" type="checkbox">
					<label for="cb4">&nbsp;Deleted</label>
				</li>
				<hr class="separator"/>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb5" type="checkbox">
					<label for="cb5">&nbsp;VAT Verified</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb6" type="checkbox">
					<label for="cb6">&nbsp;VAT Pending</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<input checked="" id="cb7" type="checkbox">
					<label for="cb7">&nbsp;VAT Invalid</label>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-xs-2">
		<div class="dropdown">
			<button class="btn btn-primary dropdown-toggle devel-dropdown-button" type="button" data-toggle="dropdown"><p class="devel-dropdown-text">Search by</p>
				<span class="caret caret-dev-top"></span></button>
			<ul class="dropdown-menu devel-dropdown-menu">
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<label>&nbsp;Client name</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<label>&nbsp;Contact number</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<label>&nbsp;Email</label>
				</li>
				<hr class="separator"/>
                {{--<li role="separator" class="divider"></li>--}}
                <li class="custom-checkbox custom-checkbox-dropdown-menu">
					<label>&nbsp;Date created</label>
				</li>
				<li class="custom-checkbox custom-checkbox-dropdown-menu">
					<label>&nbsp;Balance</label>
				</li>
			</ul>
		</div>
	</div>
</div>{{-- //END --}}

{!! Datatable::table()
	->addColumn(Utils::trans($datatable->columnFields(), $datatable->entityType))
	->setUrl(url('api/' . Utils::pluralizeEntityType($entityType) . '/' . (isset($clientId) ? $clientId : (isset($vendorId) ? $vendorId : ''))))
	->setCustomValues('entityType', Utils::pluralizeEntityType($entityType))
	->setCustomValues('clientId', isset($clientId) && $clientId)
	->setOptions('sPaginationType', 'bootstrap')
    ->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
	->render('datatable') !!}

<div class="calc"></div>

@if ($entityType == ENTITY_PAYMENT)
    @include('partials/refund_payment')
@endif

{!! Former::close() !!}

<style type="text/css">

    tr:hover, tr.hover {
        background-color: #f5f5f5 !important;
    }

    th:last-child, td:last-child {
        display: none;
    }

    th .custom-checkbox > [type="checkbox"]:checked + label::after {
        background: #ffffff;
    }

    @foreach ($datatable->rightAlignIndices() as $index)
		.listForm_{{ $entityType }} table.dataTable td:nth-child({{ $index }}) {
        text-align: right;
    }

    @endforeach

	@foreach ($datatable->centerAlignIndices() as $index)
		.listForm_{{ $entityType }} table.dataTable td:nth-child({{ $index }}) {
        text-align: center;
    }
    @endforeach


</style>

<script type="text/javascript">

	function submitForm_{{ $entityType }}(action, id) {
        if (id) {
            $('#public_id_{{ $entityType }}').val(id);
        }

        if (action == 'delete' || action == 'emailInvoice') {
            sweetConfirm(function () {
                $('#action_{{ $entityType }}').val(action);
                $('form.listForm_{{ $entityType }}').submit();
            });
        } else {
            $('#action_{{ $entityType }}').val(action);
            $('form.listForm_{{ $entityType }}').submit();
        }
    }

    $(function () {

        // Handle datatable filtering
        var tableFilter = '';
        var searchTimeout = false;

        function filterTable_{{ $entityType }}(val) {
            if (val == tableFilter) {
                return;
            }
            tableFilter = val;
            var oTable0 = $('.listForm_{{ $entityType }} .data-table').dataTable();
            oTable0.fnFilter(val);
        }

        $('#tableFilter_{{ $entityType }}').on('keyup', function () {
            if (searchTimeout) {
                window.clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(function () {
                filterTable_{{ $entityType }}($('#tableFilter_{{ $entityType }}').val());
            }, 500);
        })

        if ($('#tableFilter_{{ $entityType }}').val()) {
            filterTable_{{ $entityType }}($('#tableFilter_{{ $entityType }}').val());
        }

        $('.listForm_{{ $entityType }} .head0').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $('.listForm_{{ $entityType }} .head0 input[type=checkbox]').click();
            }
        });

        // Enable/disable bulk action buttons
        window.onDatatableReady_{{ Utils::pluralizeEntityType($entityType) }} = function () {
            $(':checkbox').click(function () {
                setBulkActionsEnabled_{{ $entityType }}();
            });

            $('.listForm_{{ $entityType }} tbody tr').unbind('click').click(function (event) {
                if (event.target.type !== 'checkbox' && event.target.type !== 'button' && event.target.tagName.toLowerCase() !== 'a') {
                    $checkbox = $(this).closest('tr').find(':checkbox:not(:disabled)');
                    var checked = $checkbox.prop('checked');
                    $checkbox.prop('checked', !checked);
                    setBulkActionsEnabled_{{ $entityType }}();
                }
            });


            $('.contextMenu').each(function (index, element) {
                var uniqueClass = 'entity_id_' + $(element).data('id');

                var row = $(element).parents('tr').first();
                row.addClass(uniqueClass);

                $.contextMenu({
                    selector: '.' + uniqueClass + ' > td',
                    items: $.contextMenu.fromMenu($(element)),
                    events: {
                        show: function (options) {
                            this.parents('tr').first().addClass('hover');
                        },
                        hide: function (options) {
                            this.parents('tr').first().removeClass('hover');
                        }
                    }
                });
            });

            // turned off hover on datatable list effect. This will be replaced with right-click-menu
            // actionListHandler();
        }

        $('.listForm_{{ $entityType }} .archive, .invoice').prop('disabled', true);
        $('.listForm_{{ $entityType }} .archive:not(.dropdown-toggle)').click(function () {
            submitForm_{{ $entityType }}('archive');
        });

        $('.listForm_{{ $entityType }} .selectAll').click(function () {
            $(this).closest('table').find(':checkbox:not(:disabled)').prop('checked', this.checked);
        });

        function setBulkActionsEnabled_{{ $entityType }}() {
            var buttonLabel = "{{ trans('texts.archive') }}";
            var $checked = $('.listForm_{{ $entityType }} tbody :checkbox:checked');
            var count = $checked.length;

            var sums = {};

            $checked.parents('tr').each(function(index, el) {
                var x = $(el).find('td').get(-2);
                var text = $(x).text().split(' ');
                var currency = text[0];
                var value = parseFloat(text[1].replace(/,/g, ''));

                if(typeof sums[currency] === 'undefined') {
                    sums[currency] = 0;
                }

                sums[currency] += value;
            });

            $('.calc').text(JSON.stringify(sums));

            $('.listForm_{{ $entityType }} button.archive, .listForm_{{ $entityType }} button.invoice').prop('disabled', !count);
            if (count) {
                buttonLabel += ' (' + count + ')';
            }
            $('.listForm_{{ $entityType }} button.archive').not('.dropdown-toggle').text(buttonLabel);
        }


        // Setup state/status filter
        $('#statuses_{{ $entityType }}').select2({
            placeholder: "{{ trans('texts.status') }}",
            //allowClear: true,
            templateSelection: function (data, container) {
                if (data.id == 'archived') {
                    $(container).css('color', '#fff');
                    $(container).css('background-color', '#f0ad4e');
                    $(container).css('border-color', '#eea236');
                } else if (data.id == 'deleted') {
                    $(container).css('color', '#fff');
                    $(container).css('background-color', '#d9534f');
                    $(container).css('border-color', '#d43f3a');
                }
                return data.text;
            }
        }).val('{{ session('entity_state_filter:' . $entityType, STATUS_ACTIVE) . ',' . session('entity_status_filter:' . $entityType) }}'.split(','))
            .trigger('change')
            .on('change', function () {
                var filter = $('#statuses_{{ $entityType }}').val();
                if (filter) {
                    filter = filter.join(',');
                } else {
                    filter = '';
                }
                var url = '{{ URL::to('set_entity_filter/' . $entityType) }}' + '/' + filter;
                $.get(url, function (data) {
                    refreshDatatable();
                })
            }).maximizeSelect2Height();

        $('#statusWrapper_{{ $entityType }}').show();

        var table = $('#DataTables_Table_0');

        /*
         $.contextMenu({
         selector: '#DataTables_Table_0 td',
         callback: function (key, options) {
         //                var cellIndex = parseInt(options.$trigger[0].cellIndex),
         //                    row = table.row(options.$trigger[0].parentNode),
         //                    rowIndex = row.index();

         alert('ToDo: ' + key);

         switch (key) {
         case 'preview' :
         break;
         case 'edit-client' :
         break;
         case 'new-task' :
         break;
         case 'new-invoice' :
         break;
         case 'new-quote' :
         break;
         case 'enter-payment' :
         break;
         default :
         break;
         }
         },
         items: {
        @if($entityType == ENTITY_CLIENT)
        "preview": {name: "Preview"},
         "edit-client": {name: "Edit Client"},
         "sep1": "---------",
         "new-task": {name: "New Task"},
         "new-invoice": {name: "New Invoice"},
         "new-quote": {name: "New Quote"},
         "sep2": "---------",
         "enter-payment": {name: "Enter Payment"},
         "enter-expense": {name: "Enter Expense"},
         "enter-credit": {name: "Enter Credit"},
         "sep3": "---------",
         "archive": {name: "Archive"},
         "delete": {name: "Delete"},
        @endif

        @if($entityType === ENTITY_PRODUCT)
        "preview": {name: "Preview"},
         "edit-product": {name: "Edit Product"},
         "sep1": "---------",
         "new-product": {name: "New Product"}
        @endif
        }
         });
         */
    });

</script>
