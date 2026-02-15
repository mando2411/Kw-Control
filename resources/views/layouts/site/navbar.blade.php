<nav
class="navbar navbar-expand-lg bg-dark bg-opacity-50 navbar-dark fixed-top"
>
<div class="container">
  <a class="navbar-brand" href="{{route('home')}}"
    ><img src="{{logo()}}" class="w-100" alt="logo img"
  /></a>
  <button
    class="navbar-toggler border-0"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent"
    aria-expanded="false"
    aria-label="Toggle navigation"
  >
    <span class="navbar-toggler-icon textMainColor">
      <i class="fa fa-sliders fs-4 pt-1"></i>
    </span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('home')}}"
          >Home</a
        >
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('about')}}"
          >about us</a
        >
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('service')}}"
          >services</a
        >
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('portfolio')}}"
          >portfolio</a
        >
      </li>
        {{-- <li class="nav-item">
            <a class="nav-link text-uppercase ms-2" href="#">pages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-uppercase ms-2" href="#">shops</a>
        </li> --}}
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('blogs')}}"
          >blogs</a
        >
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="{{route('contact')}}"
          >contacts</a
        >
      </li>
    </ul>
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="#"
          ><i class="fa fa-cart-shopping"></i
        ></a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase ms-2" href="#"
          ><i class="fa fa-magnifying-glass"></i
        ></a>
      </li>
    </ul>
  </div>
</div>
</nav>
