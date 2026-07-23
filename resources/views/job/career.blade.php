@php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
@endphp
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        {{ !empty($companySettings['header_text']) ? $companySettings['header_text']->value : config('app.name', 'J.sons Communications') }}
        - {{ __('Career') }}</title>

    <link rel="icon"
{{--          href="{{ asset(Storage::url('uploads/logo/')) . '/' . (isset($companySettings['company_favicon']) && !empty($companySettings['company_favicon']) ? $companySettings['company_favicon']->value : 'favicon.png') }}"--}}
{{--          type="image" sizes="16x16">--}}
            href="{{$logo . '/' . (isset($companySettings['company_favicon']) && !empty($companySettings['company_favicon']) ?
            $companySettings['company_favicon']->value : 'favicon.png') }}"
            type="image" sizes="16x16">

    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    

  @vite( ['resources/css/app.css',   'public\css\site.scss' , 'public/css/newapp.scss'],)
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
 
 
                    
    <div _ngcontent-app-root-c501="" class="blogpost-header header-bottom-notch">
        <div _ngcontent-app-root-c501="" class="newui-content-wrapper">
          <nav _ngcontent-app-root-c501="" aria-label="breadcrumb" class="blogpost-header-breadcrumb">
            <ol _ngcontent-app-root-c501="" class="breadcrumb">
              <li _ngcontent-app-root-c501="" class="breadcrumb-item"><a _ngcontent-app-root-c501="" href="/">Home</a></li>
              <li _ngcontent-app-root-c501="" class="breadcrumb-item"><a _ngcontent-app-root-c501="" href="/blog">Blog</a></li>
              <li _ngcontent-app-root-c501="" aria-current="page" class="breadcrumb-item active"> Software Engineering </li>
            </ol>
          </nav>
          <div _ngcontent-app-root-c501="" class="blogpost-header-content">
            <div _ngcontent-app-root-c501="" class="left-col grow"><a _ngcontent-app-root-c501="" data-kontent-element-codename="perspective_topic" class="topic" href="/blog-category/software_engineering"><span _ngcontent-app-root-c501="" class="far fa-angle-left" aria-hidden="true"></span> Jsons Communications </a>
              <h1 _ngcontent-app-root-c501="" data-kontent-element-codename="title" class="title">{{ __('High Level of Professionalism, Skill and Knowledge in every interaction. Answer questions effectively and build trust with potential customers.') }}</h1>
              <div _ngcontent-app-root-c501="" data-kontent-element-codename="author" class="author ng-star-inserted" data-gtm-vis-recent-on-screen13404360_39="23588" data-gtm-vis-first-on-screen13404360_39="23588" data-gtm-vis-total-visible-time13404360_39="100" data-gtm-vis-has-fired13404360_39="1"> by Badri Varadarajan<span _ngcontent-app-root-c501="" class="ng-star-inserted" data-gtm-vis-has-fired13404360_39="1">, Portfolio CTO </span>
                <!---->
              </div>
              <!---->
            </div>
            <div _ngcontent-app-root-c501="" class="right-col grow"><img _ngcontent-app-root-c501="" data-kontent-element-codename="cover_image" height="auto" width="auto" class="image ng-star-inserted" alt="CloudFix: A FinOps Program to Cut AWS Costs (+Keep Them Down)" src="https://assets-us-01.kc-usercontent.com/7beb5311-75a4-0049-50f5-8f58fd55aba7/817b975f-3305-424b-9cf4-db1a9e4ce7e8/CloudFix_Header_ImageV1.jpg?w=800&amp;h=500&amp;fit=clip" data-gtm-vis-has-fired13404360_39="1">
              <!---->
            </div>
          </div>
        </div>
      </div>

    <section class="slice " data-offset-top="#header-main">
        <div class="container">
            <div class="row row-grid justify-content-center">
                <div class="col-lg-8 text-center">
                    <h6 class="text-sm text-uppercase ls-2 text-muted font-weight-700">{{ __('Careers') }}</h6>
                    <h2 class="h1">{{ __('Job openings') }}</h2>
                    <p class="lead lh-180">{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>

                </div>
            </div>

        </div>
    </section>
    <!-- Table (v1) -->
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
          <a href="javascript:;" class="d-block">
            <img src="./assets/img/kit/pro/anastasia.jpg" class="img-fluid border-radius-lg">
          </a>
        </div>
      
        <div class="card-body pt-2">
          <span class="text-gradient text-primary text-uppercase text-xs font-weight-bold my-2">House</span>
          <a href="javascript:;" class="card-title h5 d-block text-darker">
            Shared Coworking
          </a>
          <p class="card-description mb-4">
            Use border utilities to quickly style the border and border-radius of an element. Great for images, buttons.
          </p>
          <div class="author align-items-center">
            <img src="./assets/img/kit/pro/team-2.jpg" alt="..." class="avatar shadow">
            <div class="name ps-3">
              <span>Mathew Glock</span>
              <div class="stats">
                <small>Posted on 28 February</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div _ngcontent-app-root-c517="" class="d-block mb-3 col mb-4 ng-tns-c517-11 ng-star-inserted"><xoc-pipeline-details _ngcontent-app-root-c517="" class="ng-tns-c517-11" _nghost-app-root-c377=""><a _ngcontent-app-root-c377="" class="jobcard card-shadow ng-star-inserted" href="/jobs/5152/skyvera/devops-engineer"><div _ngcontent-app-root-c377="" class="jobcard-card ng-star-inserted"><div _ngcontent-app-root-c377="" class="jobcard-card-logo ng-star-inserted"><img _ngcontent-app-root-c377="" fallbackformat="png" height="auto" width="auto" class="logo-img" alt="Skyvera" src="https://assets-us-01.kc-usercontent.com/7beb5311-75a4-0049-50f5-8f58fd55aba7/04024414-d905-4453-aa2b-37cfc7ea967e/Skyvera-logo-navy.png?fm=png&amp;auto=format&amp;w=160&amp;h=90&amp;"></div><!----><div _ngcontent-app-root-c377="" class="jobcard-card-info"><div _ngcontent-app-root-c377="" class="brand ng-star-inserted"> Skyvera<!----></div><h3 _ngcontent-app-root-c377="" class="title ng-star-inserted"> DevOps Engineer </h3><div _ngcontent-app-root-c377="" class="salary ng-star-inserted"><span _ngcontent-app-root-c377="" class="highlighted">$20,000</span> USD/year <!----> ($10 USD/hour) </div><div _ngcontent-app-root-c377="" class="infochips ng-star-inserted"><div _ngcontent-app-root-c377="" class="chip ng-star-inserted"><i _ngcontent-app-root-c377="" class="fas fa-map-marker-alt" aria-hidden="true"></i><span _ngcontent-app-root-c377="" class="chip-text">Remote, any timezone<!----><!----></span></div><!----><div _ngcontent-app-root-c377="" class="chip"><i _ngcontent-app-root-c377="" class="fas fa-clock" aria-hidden="true"></i><span _ngcontent-app-root-c377="" class="chip-text hours">full-time (40 hrs/week)</span></div><div _ngcontent-app-root-c377="" class="chip"><i _ngcontent-app-root-c377="" class="fas fa-door-open" aria-hidden="true"></i><span _ngcontent-app-root-c377="" class="chip-text">Long-term role</span></div></div><!----></div><div _ngcontent-app-root-c377="" class="jobcard-card-cta"><div _ngcontent-app-root-c377="" class="cta-btn primary ng-star-inserted"> Read more <i _ngcontent-app-root-c377="" aria-hidden="true" class="fal fa-long-arrow-right"></i></div><!----></div></div><!----></a><!----><!----><!----><!----><!----><!----></xoc-pipeline-details></div>
    <section class="slice slice-lg bg-secondary">
        <span class="tongue tongue-top"><i class="ti ti-angle-up"></i></span>
        <div class="container">
            <div class="mb-4 text-center">
                <h3 class=" mt-3">{{ __('We help businesses grow') }}</h3>
                <div class="fluid-paragraph mt-3">
                    <p class="lead lh-180 ">
                        {{ __('Always looking for better ways to do things, innovate and help people achieve their goals.') }}
                    </p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="table-responsive-lg">
                        <table class="table table-hover table-scale--hover table-cards align-items-center">
                            <tbody>
                            @foreach ($jobs as $job)
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div>
                                                <span class="avatar bg-primary text-white mr-4"
                                                              title="{{ __('Job Position') }}">{{ $job->position }}</span>
                                            </div>
                                            <div class="media-body media-body-custom">
                                                <a href="{{ route('job.requirement', [$job->code, !empty($job) ? (!empty($job->createdBy->lang) ? $job->createdBy->lang : 'en') : 'en']) }}"
                                                   class="h6 mb-0">{{ $job->title }}</a>
                                            </div>
                                        </div>
                                    </th>
                                    <td>

                                        @foreach (explode(',', $job->skill) as $skill)
                                            <span class="badge bg-primary p-2 px-3 rounded text-white">{{ $skill }}</span>
                                        @endforeach
                                    </td>

                                    <td><i class="ti ti-map-pin mr-3"></i><span
                                            class="h6">{{!empty($job->branches)?$job->branches->name:'-'}}</span>
                                    </td>
                                </tr>
                                <tr class="table-divider"></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<footer id="footer-main">
    <div class="footer-dark">

        <div class="container">
            <div class="row align-items-center justify-content-md-between py-4 mt-4 delimiter-top">
                <div class="col-md-6">
                    <div class="copyright text-sm font-weight-bold text-center text-md-left">
                        {{ !empty($companySettings['footer_text']) ? $companySettings['footer_text']->value : ' J.sons Communications' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                        <li class="nav-item">
                            <a class="nav-link" href="#" target="_blank">
                                <i class="ti ti-brand-dribbble"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" target="_blank">
                                <i class="ti ti-brand-instagram"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" target="_blank">
                                <i class="ti ti-brand-github"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" target="_blank">
                                <i class="ti ti-brand-facebook"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="{{ asset('js/site.core.js') }}"></script>
<script src="{{ asset('js/autosize/dist/autosize.min.js') }}"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script src="{{ asset('js/demo.js') }} "></script>
</body>

</html>
