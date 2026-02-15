<footer>
    <div class="container">
      <div class="row gy-4 justify-content-between py-5">
        <div class="col-lg-3 col-md-5">
          <div>
            <div class="contactLogo">
              <img
                src="{{logo()}}"
                class="w-50"
                alt="linoor logo"
              />
            </div>
            <p class="my-3">
              Welcome to our web design agency. Lorem ipsum simply free text
              dolor sited amet cons cing elit.
            </p>

            <div class="social-links d-flex justify-content-between">
              <a
                href="https://www.facebook.com"
                class="socialIcon contactIcon"
                ><i class="fa-brands fa-facebook-f"></i
              ></a>
              <a href="https://www.twitter.com" class="socialIcon contactIcon"
                ><i class="fa-brands fa-twitter"></i
              ></a>
              <a
                href="https://www.linkedin.com"
                class="socialIcon contactIcon"
                ><i class="fa-brands fa-linkedin-in"></i
              ></a>
              <a href="https://www.youtube.com" class="socialIcon contactIcon"
                ><i class="fa-brands fa-youtube"></i
              ></a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-5 col-sm-6">
          <div class="contactLink">
            <h4 class="mb-3 h4 fw-semibold">Explor</h4>

            <div class="row">
              <div class="col-6">
                <a href="{{route('about')}}" class="text-capitalize d-block mt-2">about us</a>

                <a href="{{route('our-team')}}" class="text-capitalize d-block mt-2"
                  >meet our team</a
                >

                <a href="{{route('portfolio')}}" class="text-capitalize d-block mt-2"
                  >our portfolio</a
                >
                <a href="{{route('blogs')}}" class="text-capitalize d-block mt-2"
                  >latest news</a
                >
                <a href="{{route('contact')}}" class="text-capitalize d-block mt-2">contact us</a>
              </div>
              <div class="col-6">
                <a href="" class="text-capitalize d-block mt-2">support</a>

                <a href="" class="text-capitalize d-block mt-2"
                  >privacy policy</a
                >

                <a href="" class="text-capitalize d-block mt-2"
                  >terms of use</a
                >
                <a href="" class="text-capitalize d-block mt-2">help</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-5 col-sm-6">
          <div class="contactLink">
            <h4 class="mb-3 h4 fw-semibold">Contact</h4>

            <a href="" class="text-capitalize d-block mt-2 d-flex">
              <i class="fa fa-location-dot textMainColor me-2 pt-1"></i>
              <span
                >66 Broklyn Street, New York United States of America</span
              >
            </a>
            <a
              href="tel:01094291525"
              class="text-capitalize d-block mt-2 d-flex"
            >
              <i class="fa fa-phone-volume textMainColor me-2 pt-1"></i>
              <span>01126600995</span>
            </a>
            <a
              href="mailto:needhelp@linoor.com"
              class="text-capitalize d-block mt-2 d-flex"
            >
              <i
                class="fa fa-envelope-circle-check textMainColor me-2 pt-1"
              ></i>
              <span>needhelp@linoor.com</span>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-md-5">
          <div>
            <h3 class="mb-3 h4 fw-semibold">Our Newsletter</h3>

            <form
              action="{{route('newsLetter')}}"
              class="d-flex bg-dark rounded-3 overflow-hidden mb-4"
              id="newsletter"
              method="POST"
            >
            @csrf
              <input
                type="email"
                name="email"
                class="px-3 w-75 rounded-end-0 bg-transparent text-white py-3 border-0"
                placeholder="Enter Address"
              />
              <button type="submit" class="btn px-3 w-25 rounded-start-0">
                <span id="btn" class="socialIcon contactIcon">
                  <i class="fa fa-envelope"></i>
                </span>
              </button>
            </form>
            <p class="">
              Sign up for our latest news & articles. We won’t give you spam
              mails.
            </p>
          </div>
        </div>
      </div>

      <div class="copyRight pt-5 text-center mt-5 fs-5">
        @ copyright 2024 by
        <a href="" class="textMainColor fw-bold">perfect soluation 4u</a>
      </div>
    </div>
  </footer>
  @push('js')
  <script>
        $("#btn").click(function (){
            $('#newsletter').submit()
        })
      $('#newsletter').on('submit', function(e) {
                  e.preventDefault()
                  axios.post($(this).attr('action'), $(this).serialize()).then((res) => {
                      toastr.success(res.data.success);

                  }).catch(error => {
                      console.log(error);
                      if(error.response.data.message){
                          toastr.error(error.response.data.message ?? '{{ __('main.unexpected-error') }}')
                      }else{
                          toastr.error(error.response.data.error ?? '{{ __('main.unexpected-error') }}')
                      }
                  }).finally()
              })
  </script>
  @endpush
