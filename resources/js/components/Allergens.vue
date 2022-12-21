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
                  placeholder="Search Allergens..."
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
              <h3 class="card-title">Allergens List</h3>

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
                    <th>Icon</th>
                    <th>Title</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="category in categories.data" :key="category.id">
                    <td>{{ category.id }}</td>
                    <td>
                      <img
                        v-bind:src="getCatImage(category.icon)"
                        height="70px"
                        width="100px"
                      />
                    </td>
                    <td>{{ category.title }}</td>
                    <td>
                      <a href="#" @click="editModel(category)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      <a href="#" @click="deleteCategory(category.id)">
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
                :data="categories"
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
              <h5 class="modal-title">Add New Allergens</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="createCategory">
              <div class="modal-body">
                <div class="form-group">
                  <div class="custom-file">
                    <label>Upload Icon</label>
                    <input
                      id="exampleInputFile1"
                      type="file"
                      name="category_image"
                      class="form-control"
                      @change="uploadCatImage"
                      :class="{ 'is-invalid': form.errors.has('icon') }"
                    />
                    <has-error :form="form" field="icon"></has-error>
                  </div>
                </div>
                <div class="form-group">
                  <label>Title</label>
                  <input
                    type="text"
                    name="title"
                    class="form-control"
                    placeholder="Enter Allergen Name"
                    v-model="form.title"
                    :class="{ 'is-invalid': form.errors.has('title') }"
                  />
                  <has-error :form="form" field="title"></has-error>
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
        aria-labelledby="editNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Update Allergen</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="updateCategory">
              <div class="modal-body">
                <div class="form-group">
                  <div class="custom-file">
                    <label>Upload Icon</label>
                    <img
                      v-bind:src="eform.currentImage"
                      height="140px"
                      width="100%"
                      style="margin-bottom: 15px"
                    />
                    <input
                      id="exampleInputFile2"
                      type="file"
                      name="category_image"
                      class="form-control"
                      @change="updateCatImage"
                      :class="{ 'is-invalid': eform.errors.has('icon') }"
                    />
                    <has-error :form="eform" field="icon"></has-error>
                  </div>
                </div>
                <div class="form-group">
                  <label>Title</label>
                  <input
                    type="text"
                    name="title"
                    class="form-control"
                    placeholder="Enter Allergen Name"
                    v-model="eform.title"
                    :class="{ 'is-invalid': eform.errors.has('title') }"
                  />
                  <has-error :form="eform" field="title"></has-error>
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
export default {
  data() {
    return {
      editmode: false,
      categories: {},
      allAllergens: {},
      keywords: null,
      form: new Form({
        id: "",
        title: "",
        icon: "",
      }),
      eform: new Form({
        id: "",
        title: "",
        icon: "",
        currentImage: "",
      }),
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/allergens?page=" + page)
        .then(({ data }) => (this.categories = data.data.allergen));

      this.$Progress.finish();
    },

    getCatImage(img) {
      return `/images/api_images/${img}`;
    },

    uploadCatImage(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      reader.onloadend = (file) => {
        this.form.icon = reader.result;
      };
      reader.readAsDataURL(file);
    },

    updateCatImage(e) {
      this.eform.icon = "";
      let file = e.target.files[0];
      let reader = new FileReader();
      reader.onloadend = (file) => {
        this.eform.icon = reader.result;
      };
      reader.readAsDataURL(file);
    },

    editModel(category) {
      this.editmode = true;
      this.eform.reset();
      this.eform.fill(category);
      this.eform.currentImage = this.getCatImage(category.icon);
      $("#editNew").modal("show");
    },

    createCategory() {
      this.$Progress.start();

      this.form
        .post("/api/allergens")
        .then((data) => {
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: data.data.message,
          });
          this.$Progress.finish();
          this.loadExerciseCategories();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },

    updateCategory() {
      this.$Progress.start();
      this.eform
        .put("/api/allergens/" + this.eform.id)
        .then((response) => {
          // success
          $("#editNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadExerciseCategories();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteCategory(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("/api/allergens/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Expertise has been deleted.", "success");
              this.loadExerciseCategories();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },

    fetch() {
      axios
        .post("/api/search-allergens", {
          keyword: this.keywords,
          categories: this.allAllergens,
        })
        .then(
          ({ data }) => (
            console.log(this.categories),
            (this.categories = {}),
            (this.categories = data.data)
          )
        );
    },
    reset() {
      document.location.reload(true);
    },

    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },

    loadExerciseCategories() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/allergens")
          .then(
            ({ data }) => (
              (this.categories = data.data.allergen),
              (this.allAllergens = data.data.allAllergens)
            )
          );
      }
    },
  },
  watch: {
    keywords: function keywords(after, before) {
      this.fetch();
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
  created() {
    this.$Progress.start();
    this.loadExerciseCategories();
    this.$Progress.finish();
  },
};
</script>
<style>
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