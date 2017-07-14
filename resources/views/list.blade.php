{!! Former::open(Utils::pluralizeEntityType($entityType) . '/bulk')->addClass('listForm_' . $entityType) !!}
    {!! Former::hidden('action')->id('action_' . $entityType) !!}
    {!! Former::hidden('public_id')->id('public_id_' . $entityType) !!}
    {!! Former::hidden('datatable')->value('true') !!}

    <div class="vue-app btn-client" id="vueapp_{{ str_random() }}">
        <?php
            /**
             * Pass table state to table component
             */
            if (isset($_SERVER['QUERY_STRING'])) {
                $queryString = $_SERVER['QUERY_STRING'];
                $index = strpos($queryString, 'state=');

                if ($index !== false) {
                    $state = substr($queryString, $index + 6);
                }
                else {
                    $state = '';
                }
            }
            else {
                $state = '';
            }
        ?>
        {{-- Entities Table --}}
        <entity-table 
            urlstate="{{ $state }}"
            entity="{{ $entityType }}"
            client-id="{{ $clientId ?? $vendorId ?? '' }}"

            @if(Auth::user()->can('create', $entityType))
                create="
                    <a class='btn btn-primary' href='{{ url(Utils::pluralizeEntityType($entityType) . '/create/' . ($clientId ?? '')) }}'>
                        <span class='{{ App\Models\EntityModel::getIcon('new-' . $entityType) }} icon-create-btn'></span>
                        {{ mtrans($entityType, "new_{$entityType}") }}
                    </a>
                "
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
    @foreach ($datatable->rightAlignIndices() as $index)
        table.dataTable td:nth-child({{ $index }}), table.dataTable th:nth-child({{ $index }}) { text-align: right; }
    @endforeach

    @foreach ($datatable->centerAlignIndices() as $index)
        table.dataTable td:nth-child({{ $index }}), table.dataTable th:nth-child({{ $index }}) { text-align: center; padding: 0; }
    @endforeach
</style>