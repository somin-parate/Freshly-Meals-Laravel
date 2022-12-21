<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Parcel Preparation</h3>
            </div>
            <div class="card-body">
              <table
                id="example1"
                class="table table-bordered dataTable dtr-inline"
                role="grid"
                aria-describedby="example1_info"
              >
                <thead>
                  <tr role="row">
                    <th>Username</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th colspan="5" style="text-align: center;">Meal Details</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Gotam</td>
                    <td>gotam@gmail.com</td>
                    <td>Address</td>
                    <th>Meal List</th>
                    <th colspan="3"></th>
                  </tr>
                  <tr>
                    <td colspan="3" rowspan="4"></td>
                    <td rowspan="4">KETO</td>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                  </tr>
                  <tr>
                    <td>Paneer</td>
                    <td>2</td>
                    <td>
                      <button type="button" class="btn btn-success">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> Done
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Dal Makhni</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-success">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> Done
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Egg</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-success">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> Done
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Bhumil</td>
                    <td>bhum@gmail.com</td>
                    <td>Address</td>
                    <th>Meal List</th>
                    <th colspan="3"></th>
                  </tr>
                  <tr>
                    <td colspan="3" rowspan="8"></td>
                    <td rowspan="4">KETO</td>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                  </tr>
                  <tr>
                    <td>Paneer</td>
                    <td>2</td>
                    <td>
                      <button type="button" class="btn btn-warning">
                        <i class="fas fa-history" aria-hidden="true"></i> Pending
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Dal Makhni</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-warning">
                        <i class="fas fa-history" aria-hidden="true"></i> Pending
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Egg</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-warning">
                        <i class="fas fa-history" aria-hidden="true"></i> Pending
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td rowspan="4">VEGAN</td>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                  </tr>
                  <tr>
                    <td>Paneer</td>
                    <td>2</td>
                    <td>
                      <button type="button" class="btn btn-warning">
                        <i class="fas fa-history" aria-hidden="true"></i> Pending
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Dal Makhni</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-success">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> Done
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Egg</td>
                    <td>3</td>
                    <td>
                      <button type="button" class="btn btn-warning">
                        <i class="fas fa-history" aria-hidden="true"></i> Pending
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </section>
</template>

<script>
export default {
  data() {
    return {
      items: {},
    };
  },
  methods: {
    loadPreparationMeal() {
      if (this.$gate.isKithcenUser()) {
        axios
          .get("/api/getPreparationList")
          .then(({ data }) => (this.items = data.data));
      }
    },
  },
  mounted() {
    // $(function () {
    //   $("#example1").DataTable({
    //     paging: true,
    //     lengthChange: false,
    //     searching: true,
    //     ordering: true,
    //     info: true,
    //     autoWidth: false,
    //     responsive: true,
    //   });
    // });
  },
  created() {
    this.$Progress.start();
    // this.loadPreparationMeal();
    this.$Progress.finish();
  },
};
</script>

<style>
</style>