@extends('header')

@section('content')
  @parent
  <!-- commenting the functionality -->
  <!-- {!! Former::open($url)->method($method)
      ->rules(['product_key' => 'required|max:255'])
      ->addClass('col-md-10 col-md-offset-1 warn-on-exit') !!} -->

  <!-- psd code start -->
  <div class="product-row fixclearElem"><!-- start of .product-row -->

      <div class="product-block block-wide"><!-- start of .product-block -->
        <div class="product-block__name">
          Images
        </div>
        <div class="product-block__remove">
          Remove All Media
        </div>
        <div class="product-block-details details-padding padd-top">
          <div class="block-details__upload">
            <div class="details-upload__center">
              <div class="upload-center__inner">
                <img src="img/icon.svg" alt="">
              </div>
              <div class="upload-center__inner">
                Upload Product Images
              </div>
              <div class="upload-center__inner">
                Drag and Drop Images or Click to Browse
              </div>
            </div>
          </div>
        </div>
      </div><!-- end of .product-block -->

    </div><!-- end of .product-row -->

    <div class="product-row fixclearElem"><!-- start of .product-row -->


      <div class="product-block float-horizontal width-new-d block-height"><!-- start of .product-block -->
        <div class="product-block__name block__name--color-green">
          Details
        </div>
        <div class="product-block-details details-padding">
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Product Name</div>
            <!--<input class="p-details-input full-w" type="text" name="" value="Adidas ULTRABOOST Men's Running Shoes">-->
            <div class="p-details-input full-w">
              Adidas ULTRABOOST Men's Running Shoes
            </div>
          </div>
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Price</div>
            <div>
              <!--<input class="p-details-input input-space" type="text" name="" value="39.99"><input class="p-details-input input-small" type="text" name="" value="39.99">-->
              <div class="p-details-input input-space">
                39.99
              </div>
              <div class="p-details-input input-small">
                &euro;<i class="fa fa-caret-down" aria-hidden="true"></i>
              </div>
            </div>
          </div>
          <div class="product-block-details__block-l-m">
            <div class="product-block-details__block-static details-small-margin">Quantity</div>
            <!-- <input class="p-details-input full-w" type="text" name="" value="500"> -->
            <div class="p-details-input full-w">
              500
            </div>
          </div>
        </div>
      </div><!-- end of .product-block -->

      <div class="product-block float-horizontal width-t-r block-height"><!-- start of .product-block -->
        <div class="product-block__name block__name--color-orange">
          Tax Rate
        </div>
      </div><!-- end of .product-block -->

      <div class="product-block float-horizontal width-desc block-height no-margin"><!-- start of .product-block -->
        <div class="product-block__name block__name--color-purple">
          Description
        </div>
        <div class="product-block-details details-padding">
          <div class="product-block-details__block">
            <div class="product-block-details__block-static details-small-margin">Description</div>
            <div class="product-block-details__block-dynamic"><textarea class="description-textarea">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla.</textarea></div>
          </div>
        </div>
      </div><!-- end of .product-block -->

    </div><!-- end of .product-row -->

    <div class="product-row fixclearElem"><!-- start of .product-row -->
      <div class="product-button">
        Save
      </div>
      <div class="product-button p-button-balck">
        Cancel
      </div>
    </div>
  <!-- psd code end -->
  <!-- start of functionality-->

  <!--
  <div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">{!! $title !!}</h3>
  </div>
  <div class="panel-body form-padding-right">

  @if ($product)
    {{ Former::populate($product) }}
    {{ Former::populateField('cost', number_format($product->cost, 2, '.', '')) }}
  @endif

  {!! Former::text('product_key')->label('texts.product') !!}
  {!! Former::textarea('notes')->rows(6) !!}

  @if ($account->hasFeature(FEATURE_INVOICE_SETTINGS))
      @if ($account->custom_invoice_item_label1)
          {!! Former::text('custom_value1')->label($account->custom_invoice_item_label1) !!}
      @endif
      @if ($account->custom_invoice_item_label2)
          {!! Former::text('custom_value2')->label($account->custom_invoice_item_label2) !!}
      @endif
  @endif

  {!! Former::text('cost') !!}
  {!! Former::text('qty')->placeholder(trans('texts.leave_empty_if_service')) !!}

  @if ($account->invoice_item_taxes)
      {!! Former::select('default_tax_rate_id')
            ->addOption('', '')
            ->label(trans('texts.tax_rate'))
            ->fromQuery($taxRates, function($model) { return $model->name . ': ' . $model->rate . '%'; }, 'id') !!}
  @endif

  </div>
  </div>

  {!! Former::actions(
      Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/products'))->appendIcon(Icon::create('remove-circle')),
      Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk'))
  ) !!}

  {!! Former::close() !!}
  -->
  <script type="text/javascript">

  $(function() {
    $('#product_key').focus();
  });

  </script>

@stop
