<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div id="sort-bar">
              <div class="form-group">
                <input
                  type="text"
                  placeholder="Search Cities..."
                  id="search-input"
                  v-model="keywords"
                  class="form-control"
                />
              </div>
              <!-- <div
                class="form-group"
                style="
                  padding-left: 7%;
                  padding-top: 0px;
                  /* background: blueviolet; */
                "
              >
                <button
                  style="
                    background: #38c172;
                    color: white;
                    border-radius: 4px;
                    height: 35px;
                    width: 84px;
                  "
                  @click="reset"
                >
                  Reset
                </button>
              </div> -->
            </div>
            <div class="card-header">
              <h3 class="card-title">Cities List</h3>

              <div class="card-tools">
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="newModal"
                >
                  <i class="fa fa-plus-square"></i>
                  Add New
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="city in cities.data" :key="city.id">
                    <td>{{ city.id }}</td>
                    <td class="text-capitalize">{{ city.city }}</td>
                    <td>{{ city.code }}</td>
                    <td>
                      <a href="#" @click="editModel(city)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      <a href="#" @click="deleteCity(city.id)">
                        <i class="fa fa-trash red"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="cities"
                @pagination-change-page="getResults"
              ></pagination>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div v-if="!$gate.isAdmin()">
        <not-found></not-found>
      </div>

      <!-- Add Modal -->
      <div
        class="modal fade"
        id="addNew"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add New City</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="createCity">
              <div class="modal-body">
                <div class="form-group">
                  <label>City Name</label>
                  <input
                    type="text"
                    name="city"
                    class="form-control"
                    placeholder="Enter City Name"
                    v-model="form.city"
                    :class="{ 'is-invalid': form.errors.has('city') }"
                  />
                  <has-error :form="form" field="city"></has-error>
                </div>
                <div class="form-group">
                  <label>City Code</label>
                  <input
                    type="text"
                    name="code"
                    class="form-control"
                    placeholder="Enter City Code"
                    v-model="form.code"
                    :class="{ 'is-invalid': form.errors.has('code') }"
                  />
                  <has-error :form="form" field="code"></has-error>
                </div>
                <div class="form-group">
                  <label class="typo__label">Select Timeslots</label>
                  <multiselect
                    v-model="value"
                    tag-placeholder="Add this as new tag"
                    placeholder="Select Timeslot"
                    label="name"
                    track-by="code"
                    :options="options"
                    :close-on-select="false"
                    :multiple="true"
                    :taggable="true"
                    @tag="addTag"
                  ></multiselect>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Close
                </button>
                <button type="submit" class="btn btn-primary">Create</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Add Modal -->

      <!-- Edit Modal -->
      <div
        class="modal fade"
        id="editNew"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit City</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="updateCity">
              <div class="modal-body">
                <div class="form-group">
                  <label>City Name</label>
                  <input
                    type="text"
                    name="city"
                    class="form-control"
                    placeholder="Enter City Name"
                    v-model="eform.city"
                    :class="{ 'is-invalid': eform.errors.has('city') }"
                  />
                  <has-error :form="eform" field="code"></has-error>
                </div>
                <div class="form-group">
                  <label>City Code</label>
                  <input
                    type="text"
                    name="code"
                    class="form-control"
                    placeholder="Enter City Code"
                    v-model="eform.code"
                    :class="{ 'is-invalid': eform.errors.has('code') }"
                  />
                  <has-error :form="eform" field="code"></has-error>
                </div>
                <div class="form-group">
                  <label class="typo__label">Select Timeslots</label>
                  <multiselect
                    v-model="value"
                    tag-placeholder="Add this as new tag"
                    placeholder="Select Timeslot"
                    label="name"
                    track-by="code"
                    :options="options"
                    :close-on-select="false"
                    :multiple="true"
                    :taggable="true"
                    @tag="addTag"
                  ></multiselect>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Close
                </button>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Edit Modal -->
    </div>
  </section>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
  components: {
    Multiselect,
  },
  data() {
    return {
      editmode: false,
      cities: {},
      allCities: {},
      value: [],
      options: [],
      keywords: null,
      form: new Form({
        id: "",
        city: "",
        code: "",
        timeslots_options: [],
      }),
      eform: new Form({
        id: "",
        city: "",
        code: "",
        timeslots_options: [],
      }),
    };
  },

  methods: {
    addTag(newTag) {
      const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000),
      };
      this.options.push(tag);
      this.value.push(tag);
    },

    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/cities?page=" + page)
        .then(({ data }) => (this.cities = data.data.cities));

      this.$Progress.finish();
    },

    loadCities() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/cities")
          .then(
            ({ data }) => (
              (this.cities = data.data.cities),
              (this.allCities = data.data.allCities)
            )
          );
      }
    },

    loadTimeSlots() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/timeslots")
          .then(({ data }) => this.bindTimesolts(data.data));
      }
    },

    bindTimesolts(slots) {
      slots.forEach((e) => {
        var object = {};
        object = { name: e.slot_time, code: e.id };
        this.options.push(object);
      });
    },

    getSelectedOptions() {
      var final_options = [];
      this.value.forEach((e) => {
        final_options.push(e.code);
      });
      if (this.editmode == true) {
        this.eform.timeslots_options = final_options;
      } else {
        this.form.timeslots_options = final_options;
      }
    },

    createCity() {
      this.$Progress.start();
      this.getSelectedOptions();
      this.form
        .post("/api/cities")
        .then((data) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: data.data.message,
          });
          this.$Progress.finish();
          this.loadCities();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },

    updateCity() {
      this.$Progress.start();
      this.getSelectedOptions();
      this.eform
        .put("/api/cities/" + this.eform.id)
        .then((data) => {
          $("#editNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: data.data.message,
          });
          this.$Progress.finish();
          this.loadCities();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteCity(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          this.form
            .delete("/api/cities/" + id)
            .then(() => {
              Swal.fire("Deleted!", "City has been deleted.", "success");
              this.loadCities();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },

    newModal() {
      this.editmode = false;
      this.value = [];
      this.form.reset();
      $("#addNew").modal("show");
    },

    editModel(city) {
      this.editmode = true;
      this.eform.reset();
      this.eform.fill(city);
      this.getSelectedTimeslots(city.timeslots);
      $("#editNew").modal("show");
    },

    getSelectedTimeslots(timeslots) {
      this.value = [];
      if (timeslots.length > 0) {
        timeslots.forEach((e) => {
          var object = {};
          object = { name: `${e.start_time} - ${e.end_time}`, code: e.id };
          this.value.push(object);
        });
      }
    },

    fetch() {
      axios
        .post("/api/search-city", {
          keyword: this.keywords,
          cities: this.allCities,
        })
        .then(
          ({ data }) => (
            console.log(this.cities),
            (this.cities = {}),
            (this.cities = data.data)
          )
        );
    },
    reset() {
      document.location.reload(true);
    },
  },
  mounted() {
    $(function () {
      $(".select2").select2();
    });
  },
  watch: {
    keywords: function keywords(after, before) {
      this.fetch();
    },
  },
  created() {
    this.$Progress.start();
    this.loadCities();
    this.loadTimeSlots();
    this.$Progress.finish();
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
#sort-bar {
  width: 80%;
  display: flex;
  flex-wrap: wrap;
  padding: 10px;
}

#search-input {
  margin-right: 10px;
}
</style>