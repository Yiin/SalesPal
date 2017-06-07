{!! Former::open(Utils::pluralizeEntityType($entityType) . '/bulk')->addClass('listForm_' . $entityType) !!}
    {!! Former::hidden('action')->id('action_' . $entityType) !!}
    {!! Former::hidden('public_id')->id('public_id_' . $entityType) !!}
    {!! Former::hidden('datatable')->value('true') !!}

    <div class="vue-app" id="vueapp_{{ str_random() }}">
        {{-- Entities Table --}}
        <entity-table 
            entity="{{ $entityType }}"
            client-id="{{ $clientId ?? $vendorId ?? '' }}"

            @if(Auth::user()->can('create', $entityType))
                create="{!! Button::primary(Icon::create('user pull-left').(mtrans($entityType, "new_{$entityType}")))->asLinkTo(url(Utils::pluralizeEntityType($entityType) . '/create/' . ($clientId ?? ''))) !!}"
            @endif
        ></entity-table>
    </div>

    @if ($entityType == ENTITY_PAYMENT)
        @include('partials/refund_payment')
    @endif
{!! Former::close() !!}


{{-- Scripts --}}
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

</script>


{{-- Styles --}}
<style type="text/css">
    th .custom-checkbox > [type="checkbox"]:checked + label::after {
        background: #ffffff;
    }

    @foreach ($datatable->rightAlignIndices() as $index)
        table.dataTable td:nth-child({{ $index }}) { text-align: right; }
    @endforeach

    @foreach ($datatable->centerAlignIndices() as $index)
        table.dataTable td:nth-child({{ $index }}) { text-align: center; }
    @endforeach
</style>