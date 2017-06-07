@extends('master')


@section('head')

  <link href="{{ asset('css/built.css') }}" rel="stylesheet" type="text/css"/>

<script type="text/javascript">

  function checkForEnter(event)
  {
    if (event.keyCode === 13){
      event.preventDefault();
      validateServerSignUp();
      return false;
    }
  }

  function logout(force)
  {
    if (force) {
      NINJA.formIsChanged = false;
    }

    if (force || NINJA.isRegistered) {
      window.location = '{{ URL::to('logout') }}' + (force ? '?force_logout=true' : '');
    } else {
      $('#logoutModal').modal('show');
    }
  }

  function hideMessage() {
    $('.alert-info').fadeOut();
    $.get('/hide_message', function(response) {
      console.log('Reponse: %s', response);
    });
  }

  window.loadedSearchData = false;
  function onSearchBlur() {
      $('#search').typeahead('val', '');
  }

  function onSearchFocus() {
    $('#search-form').show();

    if (!window.loadedSearchData) {
        window.loadedSearchData = true;
        trackEvent('/activity', '/search');
        var request = $.get('{{ URL::route('get_search_data') }}', function(data) {
          $('#search').typeahead({
            hint: true,
            highlight: true,
          }
          @if (Auth::check() && Auth::user()->account->custom_client_label1)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->account->custom_client_label1 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->account->custom_client_label1 }}</span>'
            }
          }
          @endif
          @if (Auth::check() && Auth::user()->account->custom_client_label2)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->account->custom_client_label2 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->account->custom_client_label2 }}</span>'
            }
          }
          @endif
          @foreach (['clients', 'contacts', 'invoices', 'quotes', 'navigation'] as $type)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ $type }}'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ trans("texts.{$type}") }}</span>'
            }
          }
          @endforeach
          ).on('typeahead:selected', function(element, datum, name) {
            window.location = datum.url;
          }).focus();
        });

        request.error(function(httpObj, textStatus) {
            // if the session has expried show login page
            if (httpObj.status == 401) {
                location.reload();
            }
        });
    }
  }

  $(function() {
    // auto-logout after 8 hours
    window.setTimeout(function() {
        window.location = '{{ URL::to('/logout?reason=inactivity') }}';
    }, {{ 1000 * env('AUTO_LOGOUT_SECONDS', (60 * 60 * 8)) }});

    // auto-hide status alerts
    window.setTimeout(function() {
        $(".alert-hide").fadeOut();
    }, 3000);

    /* Set the defaults for Bootstrap datepicker */
    $.extend(true, $.fn.datepicker.defaults, {
        //language: '{{ $appLanguage }}', // causes problems with some languages (ie, fr_CA) if the date includes strings (ie, July 31, 2016)
        weekStart: {{ Session::get('start_of_week') }}
    });

    if (isStorageSupported()) {
      @if (Auth::check() && !Auth::user()->registered)
        localStorage.setItem('guest_key', '{{ Auth::user()->password }}');
      @endif
    }

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-accounts').css('display') == 'block') {
            $('.user-accounts').dropdown('toggle');
        }
    });
      $('.devel-dropdown-menu').click(function(e) {
          e.stopPropagation();
      });
      $('#select-all-chkbx').click(function(event) {
          if(this.checked) {
              // Iterate each checkbox
              $(':checkbox').each(function() {
                  this.checked = true;
              });
          }
          else {
              $(':checkbox').each(function() {
                  this.checked = false;
              });
          }
      });
    @yield('onReady')

    @if (Input::has('focus'))
        $('#{{ Input::get('focus') }}').focus();
    @endif

    // Focus the search input if the user clicks forward slash
    $('#search').focusin(onSearchFocus);
    $('#search').blur(onSearchBlur);

    // manage sidebar state
    function setupSidebar(side) {
        $("#" + side + "-menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled-" + side);

            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';
            $.post('{{ url('save_sidebar_state') }}?show_' + side + '=' + toggled);

            if (isStorageSupported()) {
                localStorage.setItem('show_' + side + '_sidebar', toggled);
            }
        });

        if (isStorageSupported()) {
            var storage = localStorage.getItem('show_' + side + '_sidebar') || '0';
            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';

            if (storage != toggled) {
                setTimeout(function() {
                    $("#wrapper").toggleClass("toggled-" + side);
                    $.post('{{ url('save_sidebar_state') }}?show_' + side + '=' + storage);
                }, 200);
            }
        }
    }

    @if ( ! Utils::isTravis())
        setupSidebar('left');
        setupSidebar('right');
    @endif

    // auto select focused nav-tab
    if (window.location.hash) {
        setTimeout(function() {
            $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
        }, 1);
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (isStorageSupported() && /\/settings\//.test(location.href)) {
            var target = $(e.target).attr("href") // activated tab
            if (history.pushState) {
                history.pushState(null, null, target);
            }
            if (isStorageSupported()) {
                localStorage.setItem('last:settings_page', location.href.replace(location.hash, ''));
            }
        }
    });

    // set timeout onDomReady
    setTimeout(delayedFragmentTargetOffset, 500);

    // add scroll offset to fragment target (if there is one)
    function delayedFragmentTargetOffset(){
        var offset = $(':target').offset();
        if (offset) {
            var scrollto = offset.top - 180; // minus fixed header height
            $('html, body').animate({scrollTop:scrollto}, 0);
        }
    }

  });

</script>

@stop

@section('body')

@if ( ! Request::is('settings/account_management'))
  {{-- @include('partials.upgrade_modal') --}}
@endif

<nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="height:50px;">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="#" id="left-menu-toggle" class="menu-toggle" title="{{ trans('texts.toggle_navigation') }}">
          <div class="navbar-brand">
                <i class="fa fa-bars hide-phone" style="width:32px;float:left"></i>
                <img src="{{ asset('images/salespal-logo.svg') }}" width="163" height="auto" style="float:left"/>
          </div>
      </a>
    </div>

    <ul class="nav navbar-nav navbar-right navbar-top-drops">
                      <li class="dropdown"><a href="javascript:;" class="dropdown-toggle button-wave" data-toggle="dropdown"><i class="fa fa-bell"></i> <span class="badge badge-xs badge-warning">6</span></a>

                    <ul class="dropdown-menu dropdown-lg">
                        <li class="notify-title">
                            3 New messages
                        </li>
                        <li class="clearfix">
                            <a href="javascript:;">
                                        <span class="pull-left">
                                            <i class="fa fa-envelope"></i>
                                        </span>

                                        <span class="media-body">
                                            15 New Messages
                                            <em>20 Minutes ago</em>
                                        </span>
                            </a>
                        </li>
                        <li class="clearfix">
                            <a href="javascript:;">
                                        <span class="pull-left">
                                            <i class="fa fa-twitter"></i>
                                        </span>

                                        <span class="media-body">
                                            13 New Followers
                                            <em>2 hours ago</em>
                                        </span>
                            </a>
                        </li>
                        <li class="clearfix">
                            <a href="javascript:;">
                                        <span class="pull-left">
                                            <i class="fa fa-download"></i>
                                        </span>

                                        <span class="media-body">
                                            Download complete
                                            <em>2 hours ago</em>
                                        </span>
                            </a>
                        </li>
                        <li class="read-more"><a href="javascript:;">View All Alerts <i class="fa fa-angle-right"></i></a></li>
                    </ul>
                </li>
    </ul>

    <div class="collapse navbar-collapse" id="navbar-collapse-1">
      <div class="navbar-form navbar-right">

        @if (Auth::check())
          @if (!Auth::user()->registered)
            {!! Button::success(trans('texts.sign_up'))->withAttributes(array('id' => 'signUpButton', 'data-toggle'=>'modal', 'data-target'=>'#signUpModal', 'style' => 'max-width:100px;;overflow:hidden'))->small() !!} &nbsp;
          @elseif (Utils::isNinjaProd() && (!Auth::user()->isPro() || Auth::user()->isTrial()))
            @if (Auth::user()->account->company->hasActivePromo())
                {!! Button::warning(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small() !!} &nbsp;
            @else
                {!! Button::success(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small() !!} &nbsp;
            @endif
          @endif
        @endif

      </div>

      @if (false && Utils::isAdmin())
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
           @section('self-updater')
            <a href="{{ URL::to('self-update') }}" class="dropdown-toggle">
              <span class="glyphicon glyphicon-cloud-download" title="{{ trans('texts.update_invoiceninja_title') }}"></span>
            </a>
          @show
        </li>
      </ul>
      @endif

      <ul class="nav navbar-nav hide-non-phone" style="font-weight: bold">
        @foreach ([
            'dashboard' => false,
            'clients' => false,
            'products' => false,
            'invoices' => false,
            'payments' => false,
            'recurring_invoices' => 'recurring',
            'credits' => false,
            'quotes' => false,
            'tasks' => false,
            'expenses' => false,
            'vendors' => false,
            'reports' => false,
            'settings' => false,
        ] as $key => $value)
            {!! Form::nav_link($key, $value ?: $key) !!}
        @endforeach
      </ul>
    </div><!-- /.navbar-collapse -->

</nav>

<div id="wrapper" class='{!! session(SESSION_LEFT_SIDEBAR) ? 'toggled-left' : '' !!} {!! session(SESSION_RIGHT_SIDEBAR, true) ? 'toggled-right' : '' !!}'>
    <nav class="">
      <div class="">
        <div id="left-sidebar-wrapper" class="hide-phone">
              <div class="btn-group user-dropdown">
              <button type="button" class="btn btn-default btn-sm devel-dropdown-toggle" data-toggle="dropdown">
                <div id="myAccountButton" class="ellipsis" style="max-width:{{ Utils::hasFeature(FEATURE_USERS) ? '130' : '100' }}px;">
                    @if (session(SESSION_USER_ACCOUNTS) && count(session(SESSION_USER_ACCOUNTS)))
                        {{ Auth::user()->account->getDisplayName() }}
                    @else
                        {{ Auth::user()->getDisplayName() }}
                    @endif
                  <span class="caret"></span>
                </div>
              </button>
              <div class="profile-photo-container">
              <li class="nav-header">
                        <div class="dropdown side-profile text-left">
                                    <span style="display: block;">
                                        <img alt="image" class="img-circle" src="{{ asset('images/placeholder.png') }}" width="40">
                                    </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        <span class="clear" style="display: block;"> <span class="block m-t-xs">                     @if (session(SESSION_USER_ACCOUNTS) && count(session(SESSION_USER_ACCOUNTS)))
                        {{ Auth::user()->account->getDisplayName() }}
                    @else
                        {{ Auth::user()->getDisplayName() }}
                    @endif  <b class="caret"></b>
                                            </span></span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs devel-dropdown-sidebar">
                                <li><a href="javascript:;"><i class="fa fa-user"></i>My Profile</a></li>
                                <li><a href="javascript:;"><i class="fa fa-calendar"></i>My Calendar</a></li>
                                <li><a href="javascript:;"><i class="fa fa-envelope"></i>My Inbox</a></li>
                                <li><a href="javascript:;"><i class="fa fa-barcode"></i>My Task</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:;"><i class="fa fa-lock"></i>Screen lock</a></li>
                                <li><a href="javascript:;"><i class="fa fa-key"></i>Logout</a></li>
                            </ul>
                        </div>
                </li>
              </div>
              <ul class="dropdown-menu user-accounts" style="display: none">
                @if (session(SESSION_USER_ACCOUNTS))
                    @foreach (session(SESSION_USER_ACCOUNTS) as $item)
                        @if ($item->user_id == Auth::user()->id)
                            @include('user_account', [
                                'user_account_id' => $item->id,
                                'user_id' => $item->user_id,
                                'account_name' => $item->account_name,
                                'user_name' => $item->user_name,
                                'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                                'selected' => true,
                            ])
                        @endif
                    @endforeach
                    @foreach (session(SESSION_USER_ACCOUNTS) as $item)
                        @if ($item->user_id != Auth::user()->id)
                            @include('user_account', [
                                'user_account_id' => $item->id,
                                'user_id' => $item->user_id,
                                'account_name' => $item->account_name,
                                'user_name' => $item->user_name,
                                'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                                'selected' => false,
                            ])
                        @endif
                    @endforeach
                @else
                    @include('user_account', [
                        'account_name' => Auth::user()->account->name ?: trans('texts.untitled'),
                        'user_name' => Auth::user()->getDisplayName(),
                        'logo_url' => Auth::user()->account->getLogoURL(),
                        'selected' => true,
                    ])
                @endif
                <li class="divider"></li>
                @if (Utils::isAdmin())
                  @if (count(session(SESSION_USER_ACCOUNTS)) > 1)
                      <li>{!! link_to('/manage_companies', trans('texts.manage_companies')) !!}</li>
                  @elseif (!session(SESSION_USER_ACCOUNTS) || count(session(SESSION_USER_ACCOUNTS)) < 5)
                      <li>{!! link_to('#', trans('texts.add_company'), ['onclick' => 'showSignUp()']) !!}</li>
                  @endif
                @endif
                <li>{!! link_to('#', trans('texts.logout'), array('onclick'=>'logout()')) !!}</li>
              </ul>
            <ul class="sidebar-nav">
                @foreach([
                    'dashboard',
                    'test',
                    'clients',
                    'products',
                    'invoices',
                    'payments',
                    'recurring_invoices',
                    'credits',
                    'quotes',
                    'tasks',
                    'expenses',
                    'vendors',
                ] as $option)
                @if (in_array($option, ['dashboard', 'settings'])
                    || Auth::user()->can('view', substr($option, 0, -1))
                    || Auth::user()->can('create', substr($option, 0, -1)))
                    @include('partials.navigation_option')
                @endif
                @endforeach
                @if ( ! Utils::isNinjaProd())
                    @foreach (Module::all() as $module)
                        @include('partials.navigation_option', [
                            'option' => $module->getAlias(),
                            'icon' => $module->get('icon', 'th-large'),
                        ])
                    @endforeach
                @endif
                @if (Auth::user()->hasPermission('view_all'))
                    @include('partials.navigation_option', ['option' => 'reports'])
                @endif
                @include('partials.navigation_option', ['option' => 'settings'])
            </ul>
        </div>
      </div>
    </nav>
    <!-- /#left-sidebar-wrapper------------------------------------ -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">

          @include('partials.warn_session', ['redirectTo' => '/dashboard'])

          @if (Session::has('warning'))
            <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
          @elseif (env('WARNING_MESSAGE'))
            <div class="alert alert-warning">{!! env('WARNING_MESSAGE') !!}</div>
          @endif

          @if (Session::has('message'))
            <div class="alert alert-info alert-hide">
              {{ Session::get('message') }}
            </div>
          @elseif (Session::has('news_feed_message'))
            <div class="alert alert-info">
              {!! Session::get('news_feed_message') !!}
              <a href="#" onclick="hideMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
            </div>
          @endif

          @if (Session::has('error'))
              <div class="alert alert-danger">{!! Session::get('error') !!}</div>
          @endif

          @if (!isset($showBreadcrumbs) || $showBreadcrumbs)
            {!! Form::breadcrumbs((isset($entity) && $entity->exists) ? $entity->present()->statusLabel : false) !!}
          @endif

          @yield('content')
          <br/>
          <div class="row">
            <div class="col-md-12">

              @if (Utils::isNinjaProd())
                @if (Auth::check() && Auth::user()->isTrial())
                  {!! trans(Auth::user()->account->getCountTrialDaysLeft() == 0 ? 'texts.trial_footer_last_day' : 'texts.trial_footer', [
                          'count' => Auth::user()->account->getCountTrialDaysLeft(),
                          'link' => '<a href="javascript:showUpgradeModal()">' . trans('texts.click_here') . '</a>'
                      ]) !!}
                @endif
              @else
                @include('partials.white_label', ['company' => Auth::user()->account->company])
              @endif
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

@include('partials.contact_us')
@include('partials.sign_up')
@include('partials.keyboard_shortcuts')

</div>

<p>&nbsp;</p>


@stop
