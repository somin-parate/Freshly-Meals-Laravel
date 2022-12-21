<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
      <router-link to="/dashboard" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt blue"></i>
        <p>
          Dashboard
        </p>
      </router-link>
    </li>

    @can('isAdmin')
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-user green"></i>
          <p>
            Users
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <router-link to="/users" class="nav-link">
              <i class="fa fa-users nav-icon green"></i>
              <p>Panel Users</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/freshly_users" class="nav-link">
              <i class="fa fa-users nav-icon green"></i>
              <p>Freshly Users</p>
            </router-link>
          </li>
         </ul>
      </li>

      <li class="nav-item">
        <router-link to="/timeslots" class="nav-link">
          <i class="nav-icon fas fa-clock cyan"></i>
          <p>Delivery Timeslots</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/todayMeals" class="nav-link">
          <i class="fa fa-utensils nav-icon red"></i>
          <p>Today's Meals</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/todayParcel" class="nav-link">
          {{-- <i class="fa fa-utensils nav-icon yellow"></i> --}}
          <i class="fa fa-shipping-fast nav-icon yellow"></i>
          <p>Today's Parcels</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/todayDelivery" class="nav-link">
          {{-- <i class="fa fa-utensils nav-icon yellow"></i> --}}
          <i class="fa fa-shipping-fast nav-icon yellow"></i>
          <p>Today's Delivery</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/banners" class="nav-link">
          <i class="nav-icon fas fa-images"></i>
          <p>Banner Images</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/cities" class="nav-link">
          <i class="nav-icon fas fa-city indigo"></i>
          <p>Cities</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/allergens" class="nav-link">
          <i class="nav-icon fas fa-allergies pink"></i>
          <p>Allergens</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/plans" class="nav-link">
          <i class="fa fa-tasks nav-icon orange"></i>
          <p>Meal Plan Types</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/foodItems" class="nav-link">
          <i class="fa fa-stroopwafel nav-icon blue"></i>
          <p>Food Items</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/meals" class="nav-link">
          <i class="fa fa-utensils nav-icon yellow"></i>
          <p>Meals</p>
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/bankRequests" class="nav-link">
          <i class="fa fa-university nav-icon white"></i>
          <p>Transaction History</p>
        </router-link>
      </li>

        <li class="nav-item">
        <router-link to="/feedback" class="nav-link">
            <i class="fas fa-comments nav-icon white"></i>
            <p>Feedbacks & Refunds</p>
        </router-link>
        </li>

        <li class="nav-item">
        <router-link to="/faqs" class="nav-link">
            <i class="fa fa-question nav-icon red"></i>
            <p>Faqs</p>
        </router-link>
        </li>

        <li class="nav-item">
        <router-link to="/notifications" class="nav-link">
            <i class="fas fa-bell nav-icon cyan"></i>
            <p>Notifications</p>
        </router-link>
        </li>
    @endcan

    @canany(['isAdmin', 'isFinanceUser'])
      <li class="nav-item">
        <router-link to="/offers" class="nav-link">
          <i class="nav-icon fas fa-tags teal"></i>
          <p>Offers</p>
        </router-link>
      </li>
    @endcanany

    @canany(['isKithcenUser'])
    <li class="nav-item">
      <router-link to="/todayMeals" class="nav-link">
        <i class="fa fa-utensils nav-icon yellow"></i>
        <p>Today's Meals</p>
      </router-link>
    </li>
    @endcanany

    @canany(['isParcelUser'])
    <li class="nav-item">
      <router-link to="/todayParcel" class="nav-link">
        <i class="fa fa-utensils nav-icon yellow"></i>
        <p>Today's Parcels</p>
      </router-link>
    </li>
    @endcanany

    <li class="nav-item">
      <a href="#" class="nav-link" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-power-off red"></i>
        <p>
          {{ __('Logout') }}
        </p>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</nav>
