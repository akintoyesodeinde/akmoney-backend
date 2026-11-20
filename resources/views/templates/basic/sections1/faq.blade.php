@php
    $faqContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', false, null, true);
    $chunks = collect($faqElements)->chunk(ceil(count($faqElements) / 2));
@endphp
<!-- FAQ Section  -->

<section class="faqs-section help-center">
    <div class="overlay pt-120 pb-120">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-7">
                    <div class="section-header text-center">
                        <h5 class="sub-title">Frequently Asked Questions</h5>
                        <h2 class="title"> {{ __(@$faqContent->data_values->heading) }}</h2>
                    </div>
                </div>
            </div>
            <div class="row cus-mar">
                <div class="col-lg-6">
                    <div class="accordion" id="accordionLeft">
                        @foreach ($chunks[0] ?? [] as $index => $faqElement)
                        @php
                            $heading = 'headingLeft_' . $index;
                            $collapse = 'collapseLeft_' . $index;
                        @endphp
                        <div class="accordion-item">
                            <h6 class="accordion-header" id="{{ $heading }}">
                                <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#{{ $collapse }}"
                                    aria-expanded="false"
                                    aria-controls="{{ $collapse }}">
                                    {{ __($faqElement->data_values->question) }}
                                </button>
                            </h6>
                            <div id="{{ $collapse }}" class="accordion-collapse collapse"
                                aria-labelledby="{{ $heading }}"
                                data-bs-parent="#accordionLeft">
                                <div class="accordion-body">
                                    {!! $faqElement->data_values->answer !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="accordion" id="accordionRight">
                        @foreach ($chunks[1] ?? [] as $index => $faqElement)
                        @php
                            $heading = 'headingRight_' . $index;
                            $collapse = 'collapseRight_' . $index;
                        @endphp
                        <div class="accordion-item">
                            <h6 class="accordion-header" id="{{ $heading }}">
                                <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#{{ $collapse }}"
                                    aria-expanded="false"
                                    aria-controls="{{ $collapse }}">
                                    {{ __($faqElement->data_values->question) }}
                                </button>
                            </h6>
                            <div id="{{ $collapse }}" class="accordion-collapse collapse"
                                aria-labelledby="{{ $heading }}"
                                data-bs-parent="#accordionRight">
                                <div class="accordion-body">
                                    {!! $faqElement->data_values->answer !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.querySelectorAll('.accordion').forEach(accordion => {
    accordion.addEventListener('click', e => {
        if (e.target.classList.contains('accordion-button')) {
            const currentlyOpen = accordion.querySelector('.accordion-button:not(.collapsed)');
            if (currentlyOpen && currentlyOpen === e.target) {
                currentlyOpen.click(); // forces close
            }
        }
    });
});
</script>